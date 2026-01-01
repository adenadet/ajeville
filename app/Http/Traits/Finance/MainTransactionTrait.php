<?php

namespace App\Http\Traits\Finance;

use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\CRM\Customer;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance as PatientInsurance;
use App\Models\EMR\Visit;
use App\Models\Finance\Account;
use App\Models\Finance\BranchBank;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalLine;
use App\Models\Finance\MainTransaction;
use App\Models\Finance\Payment;
use App\Models\Finance\Transaction;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\TopUp;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Finance\MainService;
trait MainTransactionTrait{
    use DepositTrait, ItemTrait, LogTrait, SettingTrait;

    private function financial_transaction_effect($transaction){
        switch ($transaction->trans_type) {
            case 'Debit':  // e.g. Payment made by customer → reduces balance
                return -1 * $transaction->amount;
            case 'Credit': // e.g. Invoice / Sales → increases balance
                return +1 * $transaction->amount;
            default:       // Zero → no effect
                return 0;
        }
    }
    /*
    ------------------------------------------------------------------------------------------------------------------
    Transaction Basic Functions
    1. Cancel Transaction
    2. Create Transaction
    3. Get All Transactions
    4. Get Transaction By
    5. Reverse Payment
    6. Update a Transaction
    ------------------------------------------------------------------------------------------------------------------
    */

    public function finance_main_transaction_create($data){
        $main_service = new MainService();

        $main_transaction = MainTransaction::create([
            'date' => $data['date'] ?? date('Y-m-d'),
            'payment_due_date' => $data['payment_due_date'] ?? date('Y-m-d'),
            'customer_id' => $data['customer_id'] ?? null,
            'vendor_id' => $data['vendor_id'] ?? null,
            'staff_id' => $data['staff_id'] ?? null,
            'trans_type' => $data['trans_type'],
            'transactionable_type' => $data['transactionable_type'],
            'transactionable_id' => $data['transactionable_id'],
            'unique_id' => $main_service->finance_setting_generate_unique_id('transaction'),
            'amount' => $data['amount'],
            'paid' => $data['paid'] ?? 0.00,
            'payable' => $data['payable'] ?? $data['amount'],
            'status' => $data['status'] ?? 0,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
        ]);

        return $main_transaction;
    }

    public function finance_main_transaction_get_all($type, $specific, $detailed, $paginated){
        $query = MainTransaction::query();

        switch($type){
            case 'incoming':
                $query = $query->where('trans_type', '=', 'debit')->whereIn('transactionable_type', ['sales_order',  ]);
            break;
            case 'credit_overdue':
                $query = $query->where('trans_type', '=', 'debit')->where('status', '=', MainTransaction::StatusNotPaid)->where('payment_due_date', '<=', date('Y-m-d'));
            break;
            case 'debit_overdue':
                $query = $query->where('trans_type', '=', 'credit')->where('status', '=', MainTransaction::StatusNotPaid)->where('payment_due_date', '<=', date('Y-m-d'));
            break;
            case 'outgoing':
                $query = $query->where('trans_type', '=', 'credit')->whereIn('transactionable_type', ['purchase_order', 'work_order']);
            break;
        }

        if(is_array($specific)){
            if(isset($specific['customer_id']) && !empty($specific['customer_id'])){
                $query = $query->where('customer_id', '=', $specific['customer_id']);
            }
            if(isset($specific['staff_id']) && !empty($specific['staff_id'])){
                $query = $query->where('staff_id', '=', $specific['staff_id']);
            }
            if(isset($specific['vendor_id']) && !empty($specific['vendor_id'])){
                $query = $query->where('vendor_id', '=', $specific['vendor_id']);
            }
            if(isset($specific['start_date']) && !empty($specific['start_date'])){
                $query = $query->where('date', '>=', $specific['start_date']);
            }
            if(isset($specific['end_date']) && !empty($specific['end_date'])){
                $query = $query->where('date', '<=', $specific['end_date']);
            }
        }

        $query = $detailed ? $query->with(['customer', 'staff', 'vendor']) : $query;
        $query = $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function finance_main_transaction_get_by($type, $id, $detailed){
        $query = MainTransaction::where('id', '=', $id)->orWhere('unique_id', '=', $id);
        $query = $detailed ? $query->with(['creator', 'customer', 'staff', 'vendor', 'transactionable', ]) : $query->select();
        return $query->first();
    }

    public function finance_main_transaction_update($data, $id){
        $main_transaction = MainTransaction::find($id);
        
        $main_transaction->date = $data['date'] ?? $main_transaction->date;
        $main_transaction->payment_due_date = $data['payment_due_date'] ?? $main_transaction->payment_due_date;
        $main_transaction->customer_id = $data['customer_id'] ?? $main_transaction->customer_id;
        $main_transaction->vendor_id = $data['vendor_id'] ?? $main_transaction->vendor_id;
        $main_transaction->staff_id = $data['staff_id'] ?? $main_transaction->date;
        $main_transaction->trans_type = $data['trans_type']  ?? $main_transaction->trans_type;
        $main_transaction->transactionable_type = $data['transactionable_type'] ?? $main_transaction->transactionable_type;
        $main_transaction->transactionable_id = $data['transactionable_id'] ?? $main_transaction->transactionable_id;
        $main_transaction->amount = $data['amount'] ?? $main_transaction->amount;
        $main_transaction->paid = $data['paid'] ??  $main_transaction->paid;
        $main_transaction->payable = $data['payable'] ?? $main_transaction->payable;
        $main_transaction->status = $data['status'] ?? $main_transaction->status;
        $main_transaction->updated_by = Auth::id() ?? auth('api')->id();
        
        $main_transaction->save();

        return $main_transaction;
    }

    public function finance_main_transaction_payment_made($amount, $id){
        $main_transaction = MainTransaction::find($id);
        
        if($main_transaction->paid + $amount == $main_transaction->amount){
            $main_transaction->status = MainTransaction::StatusPaid;
        }

        $main_transaction->paid += $amount;
        $main_transaction->save();

        return $main_transaction;
    }
    
    /*
    ------------------------------------------
    Generate Reports 
    --------------------------------------------
    */
    public function finance_main_transaction_branch_account_balance($account_id, $start_date, $end_date, $paginated){
        $account = BranchBank::findOrFail($account_id);

        $sumFromStartToNow = (float) MainTransaction::where('account_id', '=', $account_id)
            ->whereDate('date', '>=', $start_date)
            ->whereNull('deleted_at')
            ->select(DB::raw("COALESCE(SUM(
                    CASE
                        WHEN trans_type = 'Debit'  THEN -amount
                        WHEN trans_type = 'Credit' THEN  amount
                        ELSE 0
                    END
                ), 0) as effect_sum"))
            ->value('effect_sum');

        if (!is_null($account->balance)) {
            $openingBalance = (float) $account->balance - $sumFromStartToNow;
        } 
        else {
            // Fallback: opening = sum of all effects BEFORE start_date
            $openingBalance = (float) MainTransaction::where('account_id', $account_id)
                ->whereDate('date', '<', $start_date)
                ->whereNull('deleted_at')
                ->select(DB::raw("
                    COALESCE(SUM(
                        CASE
                          WHEN trans_type = 'Debit'  THEN -amount
                          WHEN trans_type = 'Credit' THEN  amount
                          ELSE 0
                        END
                    ), 0) as effect_sum"))->value('effect_sum');
        }
        
        $transactions = MainTransaction::where('account_id', '=', $account_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->whereNull('deleted_at')
            ->orderBy('date')
            ->orderBy('id') // stable ordering
            ->get()
            ->map(function ($t) {
                $effect = $this->transactionEffect($t);
                return [
                    'id'             => $t->id,
                    'date'           => $t->date,
                    'transactionable_type' => $t->transactionable_type,
                    'transactionable_id'   => $t->transactionable_id,
                    'trans_type'     => $t->trans_type,
                    'amount'         => (float) $t->amount,
                    'effect'         => (float) $effect,
                ];
            })->values();

        // --- Compute running balances row-by-row (starting from openingBalance) ---
        $running = round((float) $openingBalance, 2);
        $transactionsWithRunning = $transactions->map(function ($tx) use (&$running) {
            $running += $tx['effect'];
            $tx['running_balance'] = round($running, 2);
            return $tx;
        });

        // Period movement
        $periodChange = $transactions->sum('effect');

        // Closing balance = Opening + Period Movement
        $closingBalance = round($openingBalance + $periodChange, 2);

        return response()->json([
            'account'        => [
                'id'    => $account->id,
                'bank_name'  => $account->bank->name,
                'account_number' => $account->account_number,
                'account_name' => $account->account_name,
                'current_balance' => is_null($account->balance) ? null : (float) $account->balance,
            ],
            'start_date'      => $start_date,
            'end_date'        => $end_date,
            'opening_balance' => round((float) $openingBalance, 2),
            'period_change'   => round((float) $periodChange, 2),
            'closing_balance' => $closingBalance,
            'transactions'    => $transactionsWithRunning,
        ]);
    }

    public function finance_main_transaction_customer_account_balance($customer_id, $start_date, $end_date, $paginated){
        
        $customer = Customer::findOrFail($customer_id);

        $sumFromStartToNow = (float) MainTransaction::where('customer_id', $customer_id)
            ->whereDate('date', '>=', $start_date)
            ->whereNull('deleted_at')
            ->select(DB::raw("COALESCE(SUM(CASE WHEN trans_type = 'Debit'  THEN -amount WHEN trans_type = 'Credit' THEN  amount ELSE 0 END), 0) as effect_sum"))
            ->value('effect_sum');

        if (!is_null($customer->balance)) {
            $openingBalance = (float) $customer->balance - $sumFromStartToNow;
        } 
        else {
            // Fallback: opening = sum of all effects BEFORE start_date
            $openingBalance = (float) MainTransaction::where('customer_id', $customer_id)
                ->whereDate('date', '<', $start_date)
                ->whereNull('deleted_at')
                ->select(DB::raw("COALESCE(SUM(CASE WHEN trans_type = 'Debit'  THEN -amount WHEN trans_type = 'Credit' THEN  amount ELSE 0 END), 0) as effect_sum"))
                ->value('effect_sum');
        }
        
        $transactions = MainTransaction::where('customer_id', $customer_id)
            ->whereBetween('date', [$start_date, $end_date])
            ->whereNull('deleted_at')
            ->orderBy('date')
            ->orderBy('id') // stable ordering
            ->get()
            ->map(function ($t) {
                $effect = $this->transactionEffect($t);
                return [
                    'id'                    => $t->id,
                    'date'                  => $t->date,
                    'transactionable_type'  => $t->transactionable_type,
                    'transactionable_id'    => $t->transactionable_id,
                    'trans_type'            => $t->trans_type,
                    'amount'                => (float) $t->amount,
                    'effect'                => (float) $effect,
                ];
            })->values();

        // --- Compute running balances row-by-row (starting from openingBalance) ---
        $running = round((float) $openingBalance, 2);
        $transactionsWithRunning = $transactions->map(function ($tx) use (&$running) {
            $running = $running + $tx['effect'];
            $tx['running_balance'] = round($running, 2);
            return $tx;
        });

        // Period movement
        $periodChange = $transactions->sum('effect');

        // Closing balance = Opening + Period Movement
        $closingBalance = round($openingBalance + $periodChange, 2);

        return response()->json([
            'customer'        => [
                'id'    => $customer->id,
                'name'  => $customer->name,
                'email' => $customer->email,
                // you can include customer->balance if you want
                'current_balance' => is_null($customer->balance) ? null : (float) $customer->balance,
            ],
            'start_date'      => $start_date,
            'end_date'        => $end_date,
            'opening_balance' => round((float) $openingBalance, 2),
            'period_change'   => round((float) $periodChange, 2),
            'closing_balance' => $closingBalance,
            'transactions'    => $transactionsWithRunning,
        ]);
    }

    public function finance_main_transaction_customer_overdue_balance($customers, $start_date, $end_date, $paginated){

    }

    public function finance_main_transaction_customer_overdue_balance_all($customers, $zero, $paginated){
        $today = now()->toDateString();

        $report = Customer::select(
            'crm_customers.id',
            'crm_customers.name',
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
        ->join('finance_main_transactions as mt', 'mt.customer_id', '=', 'customers.id')
        ->where('mt.trans_type', 'Credit') // only invoices
        ->whereColumn('mt.amount', '>', 'mt.paid') // still outstanding
        ->groupBy('crm_customers.id', 'crm_customers.name')
        ->havingRaw('SUM(mt.amount - mt.paid) > 0') // only customers with balances
        ->orderBy('customers.name')
        ->get();

        return $report;
    }

    public function finance_main_transaction_report_cash_flow_days($numbers, $start_date = null, $end_date = null){
        $days = max(1, $numbers);

        // range: start..end (inclusive)
        $end   = Carbon::today(); // today (00:00:00)
        $start = $end->copy()->subDays($days - 1);

        // Aggregate query: group by DATE(date) and compute three sums.
        // CAST to DECIMAL keeps sums precise — adjust precision if you expect very large amounts.
        $aggregates = DB::table('finance_main_transactions')
            ->selectRaw("
                DATE(`date`) as date,
                SUM(CASE WHEN transactionable_type = 'sales_order' AND trans_type = 'debit' 
                        THEN CAST(amount AS DECIMAL(16,2)) ELSE 0 END) as total_sold,
                SUM(CASE WHEN transactionable_type = 'payment' AND trans_type = 'credit' 
                        THEN CAST(amount AS DECIMAL(16,2)) ELSE 0 END) as payments_received,
                SUM(CASE WHEN transactionable_type = 'payment' AND trans_type = 'debit' 
                        THEN CAST(amount AS DECIMAL(16,2)) ELSE 0 END) as payments_outward
            ")
            ->whereBetween('date', [$start->startOfDay()->toDateTimeString(), $end->endOfDay()->toDateTimeString()])
            ->groupBy(DB::raw('DATE(`date`)'))
            ->orderBy('date', 'desc') // returns aggregated rows newest -> oldest
            ->get();

        // Map aggregated rows by date string "YYYY-MM-DD"
        $map = [];
        foreach ($aggregates as $row) {
            $d = (string) $row->date; // "2025-10-21"
            $map[$d] = [
                'total_sold'       => (float) $row->total_sold,
                'payments_received'=> (float) $row->payments_received,
                'payments_outward' => (float) $row->payments_outward,
            ];
        }

        // Build final array for each day (newest -> oldest) and ensure floats
        $results = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $end->copy()->subDays($i);
            $ds  = $day->format('Y-m-d');
            $entry = $map[$ds] ?? ['total_sold' => 0.0, 'payments_received' => 0.0, 'payments_outward' => 0.0];

            $results[] = array_merge(['date' => $ds], $entry);
        }

        return $results;
    }
}