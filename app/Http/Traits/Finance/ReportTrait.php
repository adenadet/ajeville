<?php

namespace App\Http\Traits\Finance;

use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Equipments\AssetTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Models\CRM\Customer;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Operations\Branch;
use App\Models\Equipments\Asset;
use App\Models\Finance\Account;
use App\Models\Finance\Payment;
use App\Models\Finance\MainTransaction;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\TopUp;
use App\Models\Insurance\PlanBranch;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;
use App\Models\Sales\Order;
use Carbon\Carbon;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait ReportTrait{

    use AccountTrait, AssetTrait, CustomerTrait, ExpenseTrait, StoreTrait;

    protected array $buckets;

    protected function bucketForDays(int $days): string
    {
        foreach ($this->buckets as $label => [$min, $max]) {
            if (is_null($max)) {
                if ($days >= $min) return $label;
            } else {
                if ($days >= $min && $days <= $max) return $label;
            }
        }
        // fallback
        return array_key_first($this->buckets);
    }

    public function __construct($buckets = null)
    {
        // buckets defined as array of [min_days, max_days] - max null means infinity
        $this->buckets = $buckets ?? [
            '0-30'  => [0, 30],
            '31-60' => [31, 60],
            '61-90' => [61, 90],
            '91+'   => [91, null],
        ];
    }

    public function finance_report_balance_sheet($date, $branch = null){

        //Get Assets
        $assets = $this->equipment_asset_get_valuation_summary($date);
        $cash = $this->finance_account_get_all($branch !== null ? 'branch' : 'all' , $branch, false, false, null)->sum('balance');
        $inventory = $this->inventory_report_store_value('summary_wise', null, false, false);
        $current = $this->finance_expense_get_all('unpaid', $date, false, false, null)->sum('amount');
        $non_current = 0.00;
         
        return [
            'date' => $date,
            'assets' => [
                'current_assets' => [
                    'cash'                  => $cash,
                    'accounts_receivable'   => $accountsReceivable ?? 0,
                    'inventory'             => $inventory,
                ],
                'non_current_assets' => [
                    'fixed_assets' => $assets['total_purchase_value'] ?? 0,
                    'depreciation' => $assets['total_depreciation'] ?? 0,
                    'net' => $assets['total_net'] ?? 0,
                ]
            ],
            'total_assets' => $cash + ($accountsReceivable ?? 0.00) + $inventory + $assets['total_net'],
            'liabilities' => [
                'current' => $current,
                'non_current' => $non_current,
                'total' => $current + $non_current,
            ], //$this->getLiabilities() ?? 0,
            'equity' => 0, //$this->getEquity() ?? 0,
        ];
    }

    public function finance_report_cash_flow(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $map = $this->reportAccountMap;

        // gather cash-related reference_types from map
        $cashTypes = array_unique(array_merge(
            $map['cash_inflows'] ?? [],
            $map['cash_outflows'] ?? [],
            $map['investing'] ?? [],
            $map['financing'] ?? []
        ));

        $tx = MainTransaction::query()
            ->selectRaw("reference_type, trans_type, SUM(amount) as amt")
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('reference_type', $cashTypes)
            ->groupBy('reference_type', 'trans_type')
            ->get();

        $grouped = $tx->groupBy('reference_type')->map(function($rows) {
            $credit = $rows->where('trans_type','Credit')->sum('amt');
            $debit  = $rows->where('trans_type','Debit')->sum('amt');
            return ['credit' => (float)$credit, 'debit' => (float)$debit];
        });

        $inflow_customers = (($grouped['customer_payment']['credit'] ?? 0) - ($grouped['customer_payment']['debit'] ?? 0));
        $outflow_suppliers = (($grouped['supplier_payment']['debit'] ?? 0) - ($grouped['supplier_payment']['credit'] ?? 0));

        // investing: sales minus purchases
        $investing = (
            (($grouped['asset_sale']['credit'] ?? 0) - ($grouped['asset_sale']['debit'] ?? 0))
        ) - (
            (($grouped['asset_purchase']['debit'] ?? 0) - ($grouped['asset_purchase']['credit'] ?? 0))
        );

        // financing: receipts minus payments
        $financing = (
            (($grouped['loan_receipt']['credit'] ?? 0) - ($grouped['loan_receipt']['debit'] ?? 0))
        ) - (
            (($grouped['loan_payment']['debit'] ?? 0) - ($grouped['loan_payment']['credit'] ?? 0))
        );

        $operating = ($inflow_customers ?? 0) - ($outflow_suppliers ?? 0);
        $net = $operating + $investing + $financing;

        return [
            'period' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'operating' => round($operating,2),
            'investing' => round($investing,2),
            'financing' => round($financing,2),
            'net_change_in_cash' => round($net,2),
            'detail_by_type' => $grouped->toArray()
        ];
    }

    public function finance_report_depreciation(?string $asOf = null): array
    {
        $asOf = $asOf ? Carbon::parse($asOf) : Carbon::now();

        $assets = DB::table('assets')
            ->select('id','name','asset_code','purchase_date','cost','useful_life_years','salvage_value','depreciation_method')
            ->get();

        $lines = []; $totalCost = 0.0; $totalAccumulated = 0.0; $totalBook = 0.0;

        foreach ($assets as $a) {
            $purchase = Carbon::parse($a->purchase_date);
            $yearsPassed = $purchase->diffInDays($asOf) / 365.0;
            $cost = (float)$a->cost;
            $salvage = (float)($a->salvage_value ?? 0);
            $life = max(1, (float)($a->useful_life_years ?? 1));
            $method = $a->depreciation_method ?? 'straight';

            if ($method === 'straight') {
                $annual = ($cost - $salvage) / $life;
                $accumulated = min($annual * $yearsPassed, max(0, $cost - $salvage));
            } elseif ($method === 'declining') {
                $rate = (2 / $life);
                $book = $cost;
                $accum = 0;
                $years = floor($yearsPassed);
                for ($y=0;$y<$years;$y++) {
                    $dep = $book * $rate;
                    $dep = min($dep, $book - $salvage);
                    $accum += $dep;
                    $book -= $dep;
                }
                $partial = $yearsPassed - $years;
                if ($partial > 0) {
                    $dep = $book * $rate * $partial;
                    $dep = min($dep, $book - $salvage);
                    $accum += $dep;
                }
                $accumulated = $accum;
            } else {
                $annual = ($cost - $salvage) / $life;
                $accumulated = min($annual * $yearsPassed, max(0, $cost - $salvage));
            }

            $bookValue = $cost - $accumulated;
            $lines[] = [
                'id' => $a->id,
                'asset_code' => $a->asset_code,
                'name' => $a->name,
                'purchase_date' => $a->purchase_date,
                'cost' => round($cost,2),
                'accumulated_depreciation' => round($accumulated,2),
                'book_value' => round($bookValue,2),
                'method' => $method,
            ];

            $totalCost += $cost;
            $totalAccumulated += $accumulated;
            $totalBook += $bookValue;
        }

        return [
            'as_of' => $asOf->toDateString(),
            'lines' => $lines,
            'totals' => [
                'cost' => round($totalCost,2),
                'accumulated_depreciation' => round($totalAccumulated,2),
                'book_value' => round($totalBook,2),
            ]
        ];
    }

    public function finance_report_profit_and_loss(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $tx = MainTransaction::query()
            ->selectRaw("reference_type, SUM(CASE WHEN trans_type='Credit' THEN amount ELSE 0 END) as credit_sum, SUM(CASE WHEN trans_type='Debit' THEN amount ELSE 0 END) as debit_sum")
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('reference_type')
            ->get()
            ->keyBy('reference_type');

        $map = $this->reportAccountMap;

        $sumFor = function(array $types) use ($tx) {
            $totalCredit = 0.0; $totalDebit = 0.0;
            foreach ($types as $t) {
                if (isset($tx[$t])) {
                    $totalCredit += (float)$tx[$t]->credit_sum;
                    $totalDebit  += (float)$tx[$t]->debit_sum;
                }
            }
            return ['credit' => $totalCredit, 'debit' => $totalDebit];
        };

        $rev = $sumFor($map['revenue'] ?? []);
        $cogs = $sumFor($map['cogs'] ?? []);
        $opex = $sumFor($map['operating_expense'] ?? []);
        $othInc = $sumFor($map['other_income'] ?? []);
        $othExp = $sumFor($map['other_expense'] ?? []);

        $revenue = $rev['credit'] - $rev['debit'];
        $costOfGoods = $cogs['debit'] - $cogs['credit'];
        $operatingExpenses = $opex['debit'] - $opex['credit'];
        $otherIncome = $othInc['credit'] - $othInc['debit'];
        $otherExpense = $othExp['debit'] - $othExp['credit'];

        $grossProfit = $revenue - $costOfGoods;
        $operatingProfit = $grossProfit - $operatingExpenses;
        $netProfit = $operatingProfit + $otherIncome - $otherExpense;

        return [
            'period' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'revenue' => round($revenue,2),
            'cogs' => round($costOfGoods,2),
            'gross_profit' => round($grossProfit,2),
            'operating_expenses' => round($operatingExpenses,2),
            'operating_profit' => round($operatingProfit,2),
            'other_income' => round($otherIncome,2),
            'other_expense' => round($otherExpense,2),
            'net_profit' => round($netProfit,2),
            'detail_by_type' => $tx->map(fn($r)=> ['credit'=> (float)$r->credit_sum, 'debit'=> (float)$r->debit_sum])->toArray()
        ];
    }

    public function finance_report_receivables_aging_analysis($date, $customers = null, $branch = null){
        $today = $date ?? date('Y-m-d');
        $query = Customer::select(
            'customers.id',
            'customers.name',
            DB::raw("SUM(CASE 
                        WHEN DATEDIFF('$today', mt.payment_due_date) BETWEEN 0 AND 30 
                        THEN (mt.amount - mt.paid) ELSE 0 END) as bucket_0_30"),
            DB::raw("SUM(CASE 
                        WHEN DATEDIFF('$today', mt.payment_due_date) BETWEEN 31 AND 60 
                        THEN (mt.amount - mt.paid) ELSE 0 END) as bucket_31_60"),
            DB::raw("SUM(CASE 
                        WHEN DATEDIFF('$today', mt.payment_due_date) BETWEEN 61 AND 90 
                        THEN (mt.amount - mt.paid) ELSE 0 END) as bucket_61_90"),
            DB::raw("SUM(CASE 
                        WHEN DATEDIFF('$today', mt.payment_due_date) BETWEEN 91 AND 120 
                        THEN (mt.amount - mt.paid) ELSE 0 END) as bucket_91_120"),
            DB::raw("SUM(CASE 
                        WHEN DATEDIFF('$today', mt.payment_due_date) BETWEEN 121 AND 150 
                        THEN (mt.amount - mt.paid) ELSE 0 END) as bucket_121_150"),
            DB::raw("SUM(CASE 
                        WHEN DATEDIFF('$today', mt.payment_due_date) > 150 
                        THEN (mt.amount - mt.paid) ELSE 0 END) as bucket_over_150"),
            DB::raw("SUM(mt.amount - mt.paid) as balance")
        )
        ->join('main_transactions as mt', 'mt.customer_id', '=', 'customers.id')
        ->where('mt.trans_type', 'Credit') // only invoices
        ->whereColumn('mt.amount', '>', 'mt.paid'); // still outstanding
        $query = $customers !== null ? $query->whereIn('customers.id', $customers) : $query;
        
        $query = $query->groupBy('customers.id', 'customers.name')
        ->havingRaw('SUM(mt.amount - mt.paid) > 0') // only customers with balances
        ->orderBy('customers.name')
        ->get();

        return $query;
    }

    public function finance_report_receivables_aging_analysis_individual($date = null, $customer_id, $branch = null){
        $date ??= Carbon::today()->toDateString();
        // Step A: load invoices (sales orders) with totals and payments
        $soQuery = Order::query()
            ->with(['order_items', 'payments']) // so we can compute totals and payments
            ->whereNull('deleted_at');

        if ($customer_id) {
            $soQuery->where('customer_id', $customer_id);
        }

        // Only invoices that were created on or before asOf (we don't include future invoices)
        $soQuery->whereDate('date', '<=', $date);

        $salesOrders = $soQuery->get();

        // Build invoice lines: id, date, due_date, amount, paid_against_invoice, outstanding
        $invoiceLines = collect();

        foreach ($salesOrders as $so) {
            $invoiceAmount = (float) $so->getTotalCostAttribute(); // uses your model accessor
            // Sum explicit payments tied to this SO
            $paymentsTied = $so->payments->where('status', 'confirmed')->sum('amount');

            // Also consider MainTransaction credits that reference this sales_order (optional)
            $txCredits = 0;
            if (class_exists(MainTransaction::class)) {
                $txCredits = (float) MainTransaction::where('reference_type', 'sales_order')
                    ->where('reference_id', $so->id)
                    ->where('customer_id', $so->customer_id)
                    ->where('trans_type', 'Credit')
                    ->whereDate('date', '<=', $date)
                    ->sum('amount');
            }

            $paid = $paymentsTied + $txCredits;
            $outstanding = round($invoiceAmount - $paid, 2);

            if ($outstanding <= 0) {
                continue; // skip fully paid
            }

            $agingBaseDate = $so->payment_due_date ?? $so->date;
            $ageDays = Carbon::parse($date)->diffInDays(Carbon::parse($agingBaseDate));

            $invoiceLines->push([
                'sales_order_id' => $so->id,
                'unique_id' => $so->unique_id,
                'invoice_date' => $so->date,
                'due_date' => $so->payment_due_date,
                'amount' => $invoiceAmount,
                'paid' => $paid,
                'outstanding' => $outstanding,
                'age_days' => $ageDays,
                'customer_id' => $so->customer_id,
            ]);
        }

        // Step B: Collect unallocated payments for the customer(s)
        // Unallocated = confirmed payments up to asOf that are not tied to a sales_order
        $paymentsQuery = Payment::query()
            ->where('status', '=', Payment::StatusConfirmed)
            ->whereDate('date', '<=', $date);

        if ($customer_id) $paymentsQuery->where('customer_id', '=', $customer_id);

        $payments = $paymentsQuery->whereNull('so_id')->get();

        // Map customer => unallocated amount
        $unallocatedByCustomer = $payments->groupBy('customer_id')->map(function ($rows) {
            return (float) $rows->sum('amount');
        });

        // For multiple customers: process per customer
        $byCustomer = [];

        $invoiceLinesByCustomer = $invoiceLines->groupBy('customer_id');

        $customers = $customer_id ? [$customer_id] : array_keys($invoiceLinesByCustomer->toArray()) + $unallocatedByCustomer->keys()->toArray();
        $customers = array_unique($customers);

        foreach ($customers as $cid) {
            $lines = $invoiceLinesByCustomer->get($cid, collect())->sortBy('age_days')->values();

            $unallocated = (float) ($unallocatedByCustomer[$cid] ?? 0.0);

            // Apply unallocated FIFO to oldest invoices
            $adjustedLines = [];
            foreach ($lines as $line) {
                $out = $line['outstanding'];
                if ($unallocated > 0) {
                    $apply = min($out, $unallocated);
                    $out -= $apply;
                    $unallocated -= $apply;
                    $line['paid'] += $apply;
                    $line['outstanding'] = round($out, 2);
                }
                if ($line['outstanding'] > 0) $adjustedLines[] = $line;
            }

            // If unallocated remains, treat as credit (negative balance) — will show as unapplied credit row
            $unapplied_credit = $unallocated > 0 ? round($unallocated, 2) : 0.0;

            // Bucket the adjustedLines
            $bucketsTotals = array_fill_keys(array_keys($this->buckets), 0.0);
            $detailLines = [];

            foreach ($adjustedLines as $ln) {
                $bucket = $this->bucketForDays($ln['age_days']);
                $bucketsTotals[$bucket] += $ln['outstanding'];
                $detailLines[] = $ln + ['bucket' => $bucket];
            }

            $totalOutstanding = array_sum($bucketsTotals);

            $byCustomer= [
                'customer_id' => $cid,
                'customer' => $this->crm_customer_get_single(null, $cid, false),
                'buckets' => $bucketsTotals,
                'total_outstanding' => round($totalOutstanding, 2),
                'unapplied_credit' => $unapplied_credit,
                'invoices' => $detailLines,
            ];
        }

        return $byCustomer;
    }

    protected array $reportAccountMap = [
        'revenue' => ['sales_order', 'sales_receipt', 'sales'],
        'cogs' => ['purchase_invoice', 'cost_of_goods_sold', 'purchase'],
        'operating_expense' => ['expense', 'salary', 'rent', 'utilities', 'marketing'],
        'other_income' => ['interest_income', 'other_credit'],
        'other_expense' => ['interest_expense', 'penalty', 'other_debit'],
        'cash_inflows' => ['customer_payment','bank_receipt','cash_receipt'],
        'cash_outflows' => ['supplier_payment','bank_payment','cash_payment'],
        'investing' => ['asset_purchase','asset_sale'],
        'financing' => ['loan_receipt','loan_payment','owner_contribution','owner_withdrawal'],
    ];

    public function finance_report_trial_balance(?string $asOf = null): array
    {
        $date = $asOf ? Carbon::parse($asOf) : Carbon::now();

        $groupField = Schema::hasColumn('main_transactions', 'account_code') ? 'account_code' : 'reference_type';

        $rows = MainTransaction::query()
            ->selectRaw("$groupField as account, SUM(CASE WHEN trans_type='Debit' THEN amount ELSE 0 END) as debits, SUM(CASE WHEN trans_type='Credit' THEN amount ELSE 0 END) as credits")
            ->whereDate('date', '<=', $date->toDateString())
            ->groupBy($groupField)
            ->orderBy($groupField)
            ->get();

        $lines = []; $totalDebits = 0.0; $totalCredits = 0.0;

        foreach ($rows as $r) {
            $debit = (float)$r->debits;
            $credit = (float)$r->credits;
            $balance = $debit - $credit;
            if ($balance >= 0) {
                $dr = $balance; $cr = 0.0;
            } else {
                $dr = 0.0; $cr = -$balance;
            }
            $lines[] = [
                'account' => $r->account,
                'debit' => round($dr,2),
                'credit' => round($cr,2),
            ];
            $totalDebits += $dr;
            $totalCredits += $cr;
        }

        return [
            'as_of' => $date->toDateString(),
            'lines' => $lines,
            'totals' => ['debit' => round($totalDebits,2), 'credit' => round($totalCredits,2)],
            'balanced' => round($totalDebits - $totalCredits,2) === 0.0
        ];
    }

} 

