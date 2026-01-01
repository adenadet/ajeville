<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Finance\ExpenseTrait;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Procurement\VendorTrait;
use App\Models\Finance\Bank;
use App\Models\Finance\BranchBank;
use App\Models\Finance\PaymentMode;
use Illuminate\Http\Request;

class PayOutController extends Controller
{
    use CustomerTrait, EmployeeTrait, ExpenseTrait, VendorTrait;


    public function confirm($id)
    {
        //This should retract the payment as a payment affects many things in the system 
        $pay_out = $this->finance_pay_out_confirm($id);

        return response()->json(['pay_out' => $pay_out], is_string($pay_out) ? 500 : 200);
    }

    public function destroy($id)
    {
        //This should retract the payment as a payment affects many things in the system 
        $pay_out = $this->finance_pay_out_deactivate($id);

        return response()->json([
            'message' => 'Pay_out deleted successfully',
            'pay_out' => $pay_out,
        ],is_string($pay_out) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'pay_outs' => $this->finance_pay_out_get_all($_GET['status'] ?? 'all', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'accounts' => BranchBank::where('status', '=', 1)->with(['bank'])->orderBy('account_name', 'ASC')->get(),
            'banks' => Bank::select('id', 'bank_name')->where('status', '=', 1)->get(),
            'customers' => $this->crm_customer_get_all('active', null, false, false, null),
            'staffs' => $this->hrms_employee_get_all_active_users('all', null, false, false),
            'vendors' => $this->procurement_vendor_get_all('active', null, false, false, null),
        ]);
    }

    public function reverse($id)
    {
        //This should retract the payment as a payment affects many things in the system 
        $pay_out = $this->finance_pay_out_reverse($id);

        return response()->json([
            'pay_out' => $pay_out,
        ],is_string($pay_out) ? 500 : 200);
    }

    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'account_id'                            => 'required|integer',
            'amount'                                => 'required|numeric|min:1',
            'customer_id'                           => 'nullable|integer',
            'date'                                  => 'required|date',
            'description'                           => 'sometimes',
            'expense_id'                            => 'numeric',
            'staff_id'                              => 'nullable|integer',
            'vendor_id'                             => 'nullable|integer',
            'receiving_account_id'                  => 'nullable|integer',
            'receiving_account'                     => 'nullable|array',
            'receiving_account.bank_id'             => 'required_with:receiving_account|numeric',
            'receiving_account.account_name'        => 'sometimes|string|nullable',
            'receiving_account.account_number'      => 'sometimes|string|nullable',
        ]);

        $pay_out = $this->finance_pay_out_create($request);

        return response()->json([
            'pay_out' => $pay_out,
        ],is_string($pay_out) ? 500 : 200);
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'account_id'                            => 'required|integer',
            'amount'                                => 'required|numeric|min:1',
            'customer_id'                           => 'nullable|integer',
            'date'                                  => 'required|date',
            'description'                           => 'sometimes',
            'expense_id'                            => 'numeric',
            'staff_id'                              => 'nullable|integer',
            'vendor_id'                             => 'nullable|integer',
            'receiving_account_id'                  => 'nullable|integer',
            'receiving_account'                     => 'nullable|array',
            'receiving_account.bank_id'             => 'required_with:receiving_account|numeric',
            'receiving_account.account_name'        => 'sometimes|string|nullable',
            'receiving_account.account_number'      => 'sometimes|string|nullable',
        ]);

        $pay_out = $this->finance_pay_out_update($request, $id);

        return response()->json([
            'pay_out' => $pay_out,
        ],is_string($pay_out) ? 500 : 200);
    }
}
