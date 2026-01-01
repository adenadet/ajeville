<?php
namespace App\Services\Finance;

use Carbon\Carbon;
use App\Models\Finance\MainTransaction;
use Illuminate\Support\Facades\DB;

class CashFlowService
{
    /**
     * Generate cashflow summary
     * @param string|null $startDate
     * @param string|null $endDate
     */
    public function run(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->startOfMonth();
        $end   = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        // We'll assume transactions with reference_type or account indicating cash movement.
        // If MainTransaction has an 'account_type' or 'account_id', filter for cash/bank accounts.
        // Fallback: we use reference_type categories.
        $cashTypes = [
            'customer_payment', 'bank_receipt', 'cash_receipt', // inflows
            'supplier_payment', 'bank_payment', 'cash_payment', // outflows
            'asset_purchase', 'asset_sale',
            'loan_receipt', 'loan_payment', 'owner_contribution', 'owner_withdrawal',
        ];

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

        // Operating: customer payments (inflows) minus supplier payments (outflows)
        $inflow_customers = ($grouped['customer_payment']['credit'] ?? 0) - ($grouped['customer_payment']['debit'] ?? 0);
        $outflow_suppliers = ($grouped['supplier_payment']['debit'] ?? 0) - ($grouped['supplier_payment']['credit'] ?? 0);

        $investing = (
            ($grouped['asset_sale']['credit'] ?? 0) - ($grouped['asset_sale']['debit'] ?? 0)
        ) - (
            ($grouped['asset_purchase']['debit'] ?? 0) - ($grouped['asset_purchase']['credit'] ?? 0)
        );

        $financing = (
            ($grouped['loan_receipt']['credit'] ?? 0) - ($grouped['loan_receipt']['debit'] ?? 0)
        ) - (
            ($grouped['loan_payment']['debit'] ?? 0) - ($grouped['loan_payment']['credit'] ?? 0)
        );

        // net cash flow = operating + investing + financing
        $operating = ($inflow_customers ?? 0) - ($outflow_suppliers ?? 0);
        $net = $operating + $investing + $financing;

        return [
            'period' => ['start' => $start->toDateString(), 'end' => $end->toDateString()],
            'operating' => round($operating,2),
            'investing' => round($investing,2),
            'financing' => round($financing,2),
            'net_change_in_cash' => round($net,2),
            'detail_by_type' => $grouped->toArray(),
        ];
    }
}
