<?php
namespace App\Services\Finance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Finance\MainTransaction;

class ProfitLossService
{
    protected $accountMap;

    public function __construct($accountMap = null)
    {
        // Simple default mapping. Replace with your chart-of-accounts mapping or DB lookup.
        $this->accountMap = $accountMap ?? [
            'revenue' => ['sales_order','sales_receipt'],
            'cogs' => ['purchase_invoice','cost_of_goods_sold'],
            'operating_expense' => ['expense','salary','rent','utilities','marketing'],
            'other_income' => ['interest_income','other_credit'],
            'other_expense' => ['interest_expense','penalty','other_debit'],
        ];
    }

    public function run(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $tx = MainTransaction::query()
            ->selectRaw("reference_type, SUM(CASE WHEN trans_type='Credit' THEN amount ELSE 0 END) as credit_sum, SUM(CASE WHEN trans_type='Debit' THEN amount ELSE 0 END) as debit_sum")
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('reference_type')
            ->get()
            ->keyBy('reference_type');

        $sumFor = function(array $types) use ($tx) {
            $totalCredit = 0.0;
            $totalDebit = 0.0;
            foreach ($types as $t) {
                if (isset($tx[$t])) {
                    $totalCredit += (float)$tx[$t]->credit_sum;
                    $totalDebit += (float)$tx[$t]->debit_sum;
                }
            }
            return ['credit' => $totalCredit, 'debit' => $totalDebit];
        };

        $rev = $sumFor($this->accountMap['revenue']);
        $cogs = $sumFor($this->accountMap['cogs']);
        $opex = $sumFor($this->accountMap['operating_expense']);
        $othInc = $sumFor($this->accountMap['other_income']);
        $othExp = $sumFor($this->accountMap['other_expense']);

        // Normally revenue is credits, expenses are debits. Normalize:
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
            // optional detailed breakdown by reference_type
            'detail_by_type' => $tx->map(function($r){ return ['credit'=> (float)$r->credit_sum, 'debit'=> (float)$r->debit_sum]; })->toArray()
        ];
    }
}
