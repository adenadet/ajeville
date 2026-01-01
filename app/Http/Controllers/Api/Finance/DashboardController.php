<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\Finance\MainTransactionTrait;
use App\Http\Traits\Finance\SettingTrait;
use App\Http\Traits\Finance\TransactionTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use MainTransactionTrait, SettingTrait, TransactionTrait;
    public function index()
    {
        return response()->json([
            'accounts' => $this->finance_setting_branch_account_get_all('branch', request()->cookie('current_branch'), true, false, null),
            'cashflow_reports' => $this->finance_main_transaction_report_cash_flow_days(60, $start_date = null, $end_date = null),
            'customers' => [],
            'deposits' => [],
            'price_lists' => [],
            'overdue_payables' => $this->finance_main_transaction_get_all('debit_overdue', null, true, true),
            'overdue_receivables' => $this->finance_main_transaction_get_all('credit_overdue', null, true, true),
            'recent_transactions' => $this->finance_main_transaction_get_all('recent', null, true, true),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
