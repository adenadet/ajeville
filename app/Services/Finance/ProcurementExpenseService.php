<?php

namespace App\Services\Finance;

use App\Models\Approvals\Action;
use App\Models\CRM\Customer;
use App\Models\Finance\Expense;
use App\Models\Sales\Order;
use App\Models\Finance\Income;
use App\Models\Finance\Invoice;
use App\Models\Inventory\StoreItem;
use App\Models\Procurement\PaymentTerm;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\Vendor;
use App\Models\Procurement\WorkOrder;
use App\Models\Sales\OrderApproval;
use App\Models\Sales\OrderItem;
use App\Services\Inventory\IssuanceService;
use App\Services\Finance\MainService;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class ProcurementExpenseService
{
    public function approveExpense($expense, $data){
        /*
        ----------------------------------------------------------------------
        This is a multiple step for approving an expense
        1. Create an expense approval record
        2. Update the expense status
        3. Create a main transaction record for the expense
        ----------------------------------------------------------------------
        */
        
        DB::beginTransaction();
        try{   
            //Create Expense Approval Record
            $approval = Action::create([
                'action'            => $data['action'],            // e.g. approved, rejected, forwarded
                'description'       => $data['description'] ?? null,
                'approvable_type'   => $data['approvable_type'],   // polymorphic type (Invoice, PurchaseOrder...)
                'approvable_id'     => $expense->id, 
                'created_by'        => auth('api')->id() ?? Auth::id(),
                'updated_by'        => auth('api')->id() ?? Auth::id(),
            ]);

            //Update Expense Status
            $expense->status = ($data['action'] == 'approve') ?  Action::ACTION_APPROVED : Action::ACTION_REJECTED;
            $expense->updated_by = auth('api')->id() ?? Auth::id();
            $expense->save();

            if ($data['action'] == 'approve'){
                //Create Main Transaction Record
                $main_transaction = new MainService();
                
            }

            DB::commit();

            return $approval;
        }
        catch(Exception $e){
            DB::rollBack();
            return 'Error Approving Expense: '.$e->getMessage();
        }
    }
    
    public function generateExpenseFromPurchaseOrderId($type, $reference_id){
        switch($type){
            case 'Invoice':
                $invoice = Invoice::find($reference_id);
                $data = [
                    'branch_id' => $invoice->branch_id ?? request()->cookie('current_branch'),
                    'expenseable_id' => $invoice->id,
                    'expenseable_type' => 'App\Models\Finance\Invoice',
                    'classification_id' => $invoice->classification_id ?? null,
                    'amount' => $invoice->amount,
                    'payable' => $invoice->payable ?? $invoice->amount,
                    'date' => $invoice->date ?? date('Y-m-d'),
                    'due_date' => $invoice->due_date ?? $invoice->date ?? date('Y-m-d'),
                    'account_id' => $invoice->account_id ?? null,
                    'vendor_id' => $invoice->vendor_id ?? null,
                    'staff_id' => $invoice->staff_id ?? null,
                    'customer_id' => $invoice->customer_id ?? null,
                    'description' => $invoice->description,
                    'status' => Expense::StatusConfirmed,
                ];
            break;
            case 'PurchaseOrder':
                $purchase_order = PurchaseOrder::find($reference_id);
                $data = [
                    'branch_id' => $purchase_order->branch_id ?? request()->cookie('current_branch'),
                    'expenseable_id' => $purchase_order->id, //ID of the reference expense
                    'expenseable_type' => 'App\Models\Procurement\PurchaseOrder', //Invoice, Purchase Order, Asset, 
                    'classification_id' => $purchase_order->classification_id ?? null, //Basically expense type
                    'amount' => $purchase_order->total_amount,
                    'payable' => $purchase_order->payable ?? $purchase_order->total_amount,
                    'date' => $purchase_order->date ?? date('Y-m-d'),
                    'due_date' => $purchase_order->due_date ?? $purchase_order->date ?? date('Y-m-d'),
                    'account_id' => $purchase_order->account_id ?? null,
                    'vendor_id' => $purchase_order->vendor_id ?? null,
                    'staff_id' => $purchase_order->staff_id ?? null,
                    'customer_id' => $purchase_order->customer_id ?? null,
                    'description' => $purchase_order->description,
                    'status' => Expense::StatusConfirmed,
                ];
            break;
            case 'WorkOrder':
                $work_order = WorkOrder::find($reference_id);
                $data = [
                    'branch_id' => $work_order->branch_id ?? request()->cookie('current_branch'),
                    'expenseable_id' => $work_order->id,
                    'expenseable_type' => 'App\Models\Procurement\WorkOrder',
                    'classification_id' => $work_order->classification_id ?? null,
                    'amount' => $work_order->total_amount,
                    'payable' => $work_order->payable ?? $work_order->amount,
                    'date' => $work_order->date ?? date('Y-m-d'),
                    'due_date' => $work_order->due_date ?? $work_order->date ?? date('Y-m-d'),
                    'account_id' => $work_order->account_id ?? null,
                    'vendor_id' => $work_order->vendor_id ?? null,
                    'staff_id' => $work_order->staff_id ?? null,
                    'customer_id' => $work_order->customer_id ?? null,
                    'description' => $work_order->description,
                    'status' => Expense::StatusConfirmed,
                ];
            break;
        }
        $expense = $this->createExpense($data);
        return $expense;
    }

    public function createExpense($data){
        DB::beginTransaction();
        try{
            $main_service = new MainService();
            $expense = Expense::create([
                'unique_id' => $main_service->finance_setting_generate_unique_id('expense'),
                'branch_id' => $data['branch_id'] ?? request()->cookie('current_branch'),
                'expenseable_id' => $data['expenseable_id'], //ID of the reference expense
                'expenseable_type' => $data['expenseable_type'], //Invoice, Purchase Order, Asset, 
                'classification_id' => $data['classification_id'] ?? null, //Basically expense type
                'amount' => $data['amount'],
                'payable' => $data['payable'] ?? $data['amount'],
                'date' => $data['date']?? date('Y-m-d'),
                'due_date' => $data['due_date'] ?? $data['date'] ?? date('Y-m-d'),
                'account_id' => $data['account_id'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'staff_id' => $data['staff_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'description' => $data['description'],
                'status' => $data['status'] ?? Expense::StatusConfirmed,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            $main_transaction = new MainService();
            $transaction = $main_transaction->createTransaction([
                'date' => $expense->date,
                'payment_due_date' => $expense->due_date,
                'customer_id' => $expense->customer_id,
                'vendor_id' => $expense->vendor_id,
                'staff_id' => $expense->staff_id,
                'trans_type' => 'Credit',
                'transactionable_type' => 'App\Models\Finance\Expense',
                'transactionable_id' => $expense->id,
                'amount' => $expense->amount,
            ]);

            if (is_string($transaction) ){
                DB::rollback();
                return $transaction;
            }

            if (!empty($data['customer_id'])){
                $customer = Customer::findOrFail($data['customer_id']);
                $customer->balance -= $data['amount'];
                $customer->save();
            }

            if (!empty($data['vendor_id'])){
                $vendor = Vendor::findOrFail($data['vendor_id']);
                $vendor->balance += $data['amount'];
                $vendor->save();
            }

            DB::commit();
            return $expense;
        }
        catch(Exception $e){
            DB::rollBack();
            return 'Error Creating Expense: '.$e->getMessage();
        }
    }
}