<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Finance\ExpenseTrait;
use App\Http\Traits\Finance\MainTransactionTrait;
use App\Http\Traits\Finance\SettingTrait;
use App\Http\Traits\Operations\BranchTrait;
use App\Http\Traits\Procurement\VendorTrait;
use App\Http\Traits\Ums\UserTrait;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use BranchTrait, CustomerTrait, ExpenseTrait, MainTransactionTrait,  SettingTrait, UserTrait, VendorTrait;

    public function destroy(string $id)
    {
        $expense = $this->finance_expense_deactivate($id);
        return response()->json([
            'expense' => $expense,
        ], is_string($expense) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'expenses' => $this->finance_expense_get_all($_GET['type'] ??'all', $_GET['query'] ?? null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {

        return response()->json([
            'accounts'      => $this->finance_setting_branch_account_get_all('branch', request()->cookie('branch_id'), false, false, null),
            'branches'      => $this->operation_branch_get_all(false, false, null),
            'customers'     => $this->crm_customer_get_all('active', null, false, false, null),
            'expense_types' => $this->finance_expense_type_get_all('active', null, false, false, null),
            'transactions'  => $this->finance_main_transaction_get_all('all', null, false, false), 
            'users'         => $this->ums_user_get_all(),
            'vendors'       => $this->procurement_vendor_get_all('active', null, false, false, null)
        ]);
    }
    
    public function show(string $id)
    {
        $expense = $this->finance_expense_get_by(null, $id, true);
        return response()->json([
            'expense' => $expense,
        ], is_string($expense) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'expenseable_type' => 'sometimes',
            'branch_id' => 'sometimes|numeric',
            'classification_id' => 'numeric',
            'amount' => 'required|numeric',
            'date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'vendor_id' => 'nullable|numeric',
            'staff_id' => 'nullable|numeric',
            'customer_id' => 'nullable|numeric',
            'description' => 'sometimes|string',
        ]);

        $expense = $this->finance_expense_create($request);

        return response()->json([
            'expense' => $expense,
        ], is_string($expense) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'expenseable_type' => 'sometimes',
            'branch_id' => 'sometimes|numeric',
            'classification_id' => 'numeric',
            'amount' => 'required|numeric',
            'date' => 'sometimes|date',
            'due_date' => 'sometimes|date',
            'vendor_id' => 'nullable|numeric',
            'staff_id' => 'nullable|numeric',
            'customer_id' => 'nullable|numeric',
            'description' => 'sometimes|string',
        ]);
        
        $expense = $this->finance_expense_update($request, $id);

        return response()->json([
            'expense' => $expense,
        ], is_string($expense) ? 500 : 200);
    }
}
