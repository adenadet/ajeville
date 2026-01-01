<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Finance\SettingTrait;
use App\Http\Traits\Procurement\VendorTrait;
use Illuminate\Http\Request;
use App\Http\Traits\Sales\ReportTrait;
class ReportController extends Controller
{
    use CustomerTrait, ReportTrait, SettingTrait, VendorTrait;
    
    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        return response()->json([
            'banks' => $this->finance_setting_branch_account_get_all('active', null, true, false, null),
            'customers' => $this->crm_customer_get_all('active', null, false, false, null),
            'modes' => $this->finance_setting_payment_mode_get_all('active', null, false, null, null),
            'vendors' => $this->procurement_vendor_get_all('active', null, false, false, null)
        ]);
    }

    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        switch($request->report_type){
            case 'daily_sales':
                $report_data = $this->sales_report_daily_user_sales($request->input('start_date'), $request->input('end_date'), $request->input('users'));
            break;
            case 'sales_items':
                $report_data = $this->sales_report_item_detailed($request->input('start_date'), $request->input('end_date'), $_GET);
            break;
            case 'balance_sheet':
                $report_data = $this->finance_report_balance_sheet($request->date);
            break;
            
        }

        return response()->json([
            'report_data' => $report_data,
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
