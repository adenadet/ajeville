<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Finance\ReportTrait;
use App\Http\Traits\Finance\SettingTrait;
use App\Http\Traits\Procurement\VendorTrait;
use Illuminate\Http\Request;

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
            case 'aging_analysis_receivables':
                $report_data = [];
                if ($request->customer_type == 0){
                    $customers = $this->crm_customer_get_all('active', null, false, false, null);
                }
                else{
                    $customers = $request['customers'];
                }
                foreach($customers as $customer){
                    $new_data = $this->finance_report_receivables_aging_analysis_individual($request->input('date'), $customer['id'], null);
                    array_push($report_data, $new_data);
                }
                //$report_data = $this->finance_report_receivables_aging_analysis_individual($request->input('date'), $request->input('customers'), null
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
