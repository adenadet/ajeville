<?php
namespace App\Services\Finance;

use Carbon\Carbon;
use App\Models\Finance\MainTransaction;
use Illuminate\Support\Facades\DB;


class TrialBalanceService
{
    public function run(?string $asOf = null): array
    {
        $date = $asOf ? Carbon::parse($asOf) : Carbon::now();
        // Group by account_code if available; else reference_type
        $groupField = DB::schema_has_column('finance_main_transactions', 'account_code') ? 'account_code' : 'reference_type';

        $rows = MainTransaction::query()
            ->selectRaw("$groupField as account, SUM(CASE WHEN trans_type='Debit' THEN amount ELSE 0 END) as debits, SUM(CASE WHEN trans_type='Credit' THEN amount ELSE 0 END) as credits")
            ->whereDate('date', '<=', $date->toDateString())
            ->groupBy($groupField)
            ->orderBy($groupField)
            ->get();

        $lines = [];
        $totalDebits = 0.0;
        $totalCredits = 0.0;

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