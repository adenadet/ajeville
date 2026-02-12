<?php

namespace App\Http\Traits\Finance;

use App\Http\Traits\Finance\MainTransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Procurement\PurchaseOrderTrait;
use App\Models\Approvals\Action;
use App\Models\Finance\Expense;
use App\Models\Finance\ExpenseType;
use App\Models\Finance\Invoice;
use App\Models\Finance\MainTransaction;
use App\Models\Finance\Payment;
use App\Models\Finance\PayOut;
use App\Models\Finance\PayOutAllocation;
use App\Models\Finance\Transaction;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Insurance\PlanBranch;
use App\Models\Inventory\Item;
use App\Models\Inventory\PackageItem;
use App\Models\Operations\Branch;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;
use App\Models\Procurement\VendorAccount;
use App\Models\CRM\CustomerAccount;
use App\Models\Finance\BranchBank;
use App\Models\Hrms\EmployeeAccount;
use App\Services\Finance\ProcurementExpenseService;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Finance\MainService;
trait ExpenseTrait{
    use MainTransactionTrait, PurchaseOrderTrait, LogTrait;
    
    private function finance_generate_random_string($length = 10){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function finance_generate_unique_id($type){
        //return uniqid($type . '_');
        $code = $this->finance_generate_random_string(10);
        switch($type){
            case 'expense':
                $prefix = 'EXP';
                $query = Expense::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->finance_generate_unique_id('expense');
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'invoice':
                $prefix = 'INV';
                $query = Invoice::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->finance_generate_unique_id('invoice');
                }
                else{
                    return $prefix.'-'.$code;
                }
            case 'payout':
                $prefix = 'PYTO';
                $query = Payout::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->finance_generate_unique_id('payout');
                }
                else{
                    return $prefix.'-'.$code;
                }
        }
    }

    /*
    ----------------------------------------------------------------------
    Basic Expenses CRUD
    -----------------------------------------------------------------------
    */
    public function finance_expense_complete($id){
        DB::beginTransaction();

        try{
            $query = Expense::where('id', '=', $id)->orWhere('uuid', '=', $id)->first();

            $query->status = Expense::StatusPaid;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            DB::commit();
            $this->log_user_activity('Finance Expense Confirm', $id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense Confirm', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_expense_confirm($data, $id){
        DB::beginTransaction();

        try{
            $query = Expense::where('id', '=', $id)->orWhere('uuid', '=', $id)->first();

            $query->status = Expense::StatusConfirmed;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            DB::commit();
            $this->log_user_activity('Finance Expense Confirm', $id, true);
            
            return $query;

        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense Confirm', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_expense_create($data){
        $procurementExpenseService = new ProcurementExpenseService();
        $query = $procurementExpenseService->createExpense($data);
        
        if (is_string($query)){
            $this->log_user_activity('Finance Expense Create', null, false);
            return $query;    
        }
        $this->log_user_activity('Finance Expense Create', $query->id, true);
        return $query;
    }

    public function finance_expense_create_from($type, $id){
        switch ($type){
            case 'invoice':
                $query = Invoice::findOrFail($id);
                
                $full_data = array(
                    //`unique_id`, `date`, `due_date`, `branch_id`, `classification_id`, `expenseable_type`, `expenseable_id`, `account_id`, `amount`, `payable`, `vendor_id`, `customer_id`, `staff_id`, `description`, `status`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`
                    'branch_id' => $query->branch_id,
                    'amount' => $query->amount,
                    'date' => $query->date,
                    'due_date' => $query->due_date,
                    'vendor_id' => $query->vendor_id,
                    'description' => $query->description,
                    'expenseable_id' => $query->id,
                    'expenseable_type' => 'App\Models\Finance\Invoice',
                    'status' => Expense::StatusConfirmed,
                );
            break;
            case 'purchase_order':
                $query = $this->procurement_purchase_order_get_by(null, $id, true);

                $full_data = array(
                    'amount' => $query->total_amount,
                    'branch_id' => $query->branch_id,
                    'date' => $query->date,
                    'due_date' => $query->due_date,
                    'expenseable_id' => $query->id,
                    'expenseable_type' => 'App\Models\Finance\PurchaseOrder',
                    'payment_date' => $query->payment_due_date,
                    'vendor_id' => $query->vendor_id,
                    'status' => Expense::StatusConfirmed,
                );
            break;
            case 'work_order':
                $query = $this->procurement_purchase_order_get_by(null, $id, true);

                $full_data = array(
                    'amount' => $query->total_amount,
                    'branch_id' => $query->branch_id,
                    'date' => $query->date,
                    'due_date' => $query->due_date,
                    'expenseable_id' => $query->id,
                    'expenseable_type' => 'App\Models\Finance\WorkOrder',
                    'payment_date' => $query->payment_due_date,
                    'vendor_id' => $query->vendor_id,
                    'status' => Expense::StatusConfirmed,
                );
            break;
        }

        $expense = $this->finance_expense_create($full_data);

        return $expense;
    }
    public function finance_expense_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Expense::where('id', '=', $id)->orWhere('unique_id', '=', $id)->first();

            $query->status = Expense::StatusDeleted;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            DB::commit();
            $this->log_user_activity('Finance Expense Deactivate', $id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_expense_delete($id){
        DB::beginTransaction();

        try{
            $query = Expense::where('id', '=', $id)->orWhere('uuid', '=', $id)->first();

            $query->deleted_at = date('Y-m-d H:i:s');
            $query->deleted_by = auth('api')->id() ?? Auth::id();

            $query->save();
            
            DB::commit();
            $this->log_user_activity('Finance Expense Delete', $id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense Delete', $id, false);
            return $e->getMessage();
        }
        
    }

    public function finance_expense_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Expense::query();

        switch($type){
            case 'confirmed':
                $query = $query->where('status', '=', Expense::StatusConfirmed);
            break;
            case 'deleted':
                $query = $query->where('status', '=', Expense::StatusDeleted)->withTrashed();
            break;
            case 'paid':
                $query = $query->where('status', '=', Expense::StatusPaid);
            break;
            case 'unconfirmed':
                $query = $query->where('status', '=', Expense::StatusUnconfirmed);
            break;
            case 'queried':
                $query = $query->where('status', '=', Expense::StatusQueried);
            break;
            case 'rejected':
                $query = $query->where('status', '=', Expense::StatusRejected);
            break; 
            case 'unpaid':
                $query = $query->whereIn('status', [Expense::StatusQueried, Expense::StatusConfirmed, Expense::StatusUnconfirmed]);
            break;
        }

        if ($specific !== null){}

        $query = $detailed ? $query->with(['creator', 'deleter', 'expenseable', 'updater', 'vendor', 'customer', 'staff']) : $query->select('id', 'date', 'amount', 'vendor_id', 'date')->with(['vendor']);
        $query = $query->orderBy('due_date', 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function finance_expense_get_by($type, $id, $detailed){
        try{
            $query = Expense::where('id', '=', $id)->orWhere('unique_id', '=', $id);

            $query = $detailed ? $query->with(['allocations.pay_out', 'creator', 'customer', 'deleter', 'expenseable', 'updater', 'vendor.accounts.bank', 'staff']) : $query->select('id', 'date', 'amount', 'vendor_id', 'date')->with(['vendor']);
            
            return $query->firstOrFail();
        }
        catch(Exception $e){ 
            return $e->getMessage();
        }
    }

    public function finance_expense_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Expense::where('id', '=', $id)->orWhere('uuid', '=', $id)->first();

            $query->unique_id = $data['unique_id'] ?? $query->unique_id;
            $query->branch_id = $data['branch_id'] ?? $query->branch_id;
            $query->expenseable_id = $data['expenseable_id'] ?? $query->expenseable_id; //ID of the reference expense
            $query->expense_type = $data['expense_type'] ?? $query->expenseable_type; //Invoice, Purchase Order, Asset, 
            $query->classification_id = $data['classification_id'] ?? $query->classification_id; //Basically expense type
            $query->amount = $data['amount'] ?? $query->amount;
            $query->payable = $data['payable'] ?? $query->payable;
            $query->date = $data['date']?? $query->date;
            $query->due_date = $data['due_date'] ?? $query->due_date;
            $query->account_id = $data['account_id'] ?? $query->account_id;
            $query->vendor_id = $data['vendor_id'] ?? $query->vendor_id;
            $query->staff_id = $data['staff_id'] ?? $query->staff_id;
            $query->customer_id = $data['customer_id'] ?? $query->customer_id;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? $query->status;          
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();
            DB::commit();
            $this->log_user_activity('Finance Expense Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    ----------------------------------------------------------------------
    Basic Expenses Type CRUD
    -----------------------------------------------------------------------
    */
    public function finance_expense_type_create($data){
        DB::beginTransaction();

        try{
            $query = ExpenseType::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            $query->save();

            DB::commit();
            $this->log_user_activity('Finance Expense Type Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense Type Create', null, false);
            return $e->getMessage();
        }
    }

    public function finance_expense_type_deactivate($id){
        DB::beginTransaction();

        try{
            $query = ExpenseType::find($id);

            $query->status = $query->status == 1 ? 0 : 1; 
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();
            
            DB::commit();
            $this->log_user_activity('Finance Expense Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense Update', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_expense_type_get_all($type, $specific, $detailed, $paginated, $page){
        $query = ExpenseType::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'deleted':
                $query = $query->withTrashed()->where('status', '=', 0);
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0);
            break;
        }

        if($specific !== null){
            $query = $query->where('name', 'LIKE', "%$specific%");
        }

        $query = $detailed ? $query->with(['creator', 'updater']) : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function finance_expense_type_get_by($type, $id, $detailed){
        $query = ExpenseType::where('id', '=', $id);

        $query = $detailed ? $query->with(['creator', 'updater']) : $query->select('id', 'name');

        return $query->first();
    }

    public function finance_expense_type_update($data, $id){
        DB::beginTransaction();
        try{
            $query = ExpenseType::findOrFail($id);

            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->descrption;
            $query->status = $data['status'] ?? 1; 
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();
            
            DB::commit();
            $this->log_user_activity('Finance Expense Type Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense Type Update', $id, false);
            return $e->getMessage();
        }
    }

        public function finance_invoice_confirm($data, $id){
        DB::beginTransaction();
        
        try{
            $query = Invoice::where('id', '=',  $id)->orWhere('unique_id', '=', $id)->firstOrFail();

            $approval = Action::create([
                'decision'          => $data['action'],
                'reference_id'      => $id,
                'reference_type'    => 'Invoice',
                'description'       => $data['description'],
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            if ($data['action'] == 'reject'){
                $query->status = Invoice::StatusRejected;
            }
            else{
                $query->status = Invoice::StatusConfirmed;
                $expense = $this->finance_expense_create_from('invoice', $id);
                if(is_string($expense)){
                    DB::rollBack();
                    $this->log_user_activity('Finance Invoice Created', null, false);
                    return $expense;
                }
            }
            
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->updated_at = date('Y-m-d H:i:s');

            $query->save();
            
            DB::commit();
            $this->log_user_activity('Finance Invoice Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Invoice Created', null, false);
            return $e->getMessage();
        }
    }

    
    /*
    ----------------------------------------------------------------------
    Basic Finance Invoice CRUD Functions
    -----------------------------------------------------------------------
    */

    public function finance_invoice_create($data){
        DB::beginTransaction();
        
        try{
            $query = Invoice::create([
                'amount' => $data['amount'],
                'branch_id' => $data['branch_id'] ?? request()->cookie('branch_id'),
                'date' => $data['date'] ?? date('Y-m-d'),
                'description' => $data['description'] ?? null,
                'due_date' => $data['due_date'] ?? date('Y-m-d'), 
                'classification_id' => $data['expense_type_id'] ?? null,
                'invoice_number' => $data['invoice_number'] ?? null, 
                'status' => $data['status'] ?? Invoice::StatusIssued, 
                'unique_id' => $this->finance_generate_unique_id('invoice'), 
                'vendor_id' => $data['vendor_id'],
                'created_by' => auth('api')->id() ?? Auth::id(), 
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('Finance Invoice Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Invoice Created', null, false);
            return $e->getMessage();
        }
    }

    public function finance_invoice_deactivate($id){}

    public function finance_invoice_get_all($type, $specific, $detailed, $paginated){
        $query = Invoice::query();
        switch($type){
            case 'customer':
                $query = $query->with('customer')->orderBy('id','desc');
            break;
            case 'outgoing':
                $query = $query->with('vendor')->orderBy('id','desc');
            break;
            case 'unapproved':
                $query = $query->where('status', '=', Invoice::StatusIssued);
            break;
        }

        $query = $detailed ? $query->with(['branch', 'classification', 'creator', 'deleter', 'expense', 'updater', 'vendor']) : $query->select('id', 'invoice_number', 'amount', 'unique_id');
        $query = $query->orderBy('date', 'desc');
        $query = $paginated ? $query->paginate(15) : $query->get();

        return $query;
    }

    public function finance_invoice_get_by_id($id, $detailed){
        try{
            $query = Invoice::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            
            $query = $detailed ? $query->with(['branch', 'creater', 'customer', 'deleter', 'expense', 'updater', 'vendor']) : $query->select('id', 'invoice_number', 'amount', 'unique_id');
            
            $query->firstOrFail();
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function finance_invoice_mark_as_completed($data, $id){
        DB::beginTransaction();
        
        try{
            $query = Invoice::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();
            
            $query->amount = $data['amount'] ?? $query->amount;
            $query->branch_id = $data['branch_id'] ?? $query->branch_id;
            $query->date = $data['date'] ?? $query->date; 
            $query->description = $data['description'] ?? $query->description;
            $query->due_date = $data['due_date'] ?? $query->due_date; 
            $query->invoice_number = $data['invoice_number'] ?? $query->invoice_number;
            $query->vendor_id = $data['vendor_id'] ?? $query->vendor_id;
            $query->status = $data['status'] ?? $query->status; 
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Finance Invoice Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Invoice Updated', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_invoice_update($data, $id){
        DB::beginTransaction();
        
        try{
            $query = Invoice::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();
            
            $query->amount = $data['amount'] ?? $query->amount;
            $query->branch_id = $data['branch_id'] ?? $query->branch_id;
            $query->date = $data['date'] ?? $query->date; 
            $query->description = $data['description'] ?? $query->description;
            $query->classification_id = $data['classification_id'] ?? $query->classification_id;
            $query->due_date = $data['due_date'] ?? $query->due_date; 
            $query->invoice_number = $data['invoice_number'] ?? $query->invoice_number;
            $query->vendor_id = $data['vendor_id'] ?? $query->vendor_id;
            $query->status = $data['status'] ?? $query->status; 
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Finance Invoice Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Invoice Updated', $id, false);
            return $e->getMessage();
        }
    }

    /*
    ----------------------------------------------------------------------
    Pay Out Basic Functions
    -----------------------------------------------------------------------
    */

    public function finance_pay_out_confirm($id){
        try{
            $payout = PayOut::with('vendor')->findOrFail($id);

            if ($payout->status == PayOut::StatusConfirmed) {return 'Payout already confirmed.';}
            if ($payout->status == PayOut::StatusReversed) {return 'Payout has been reversed and cannot be confirmed.';}

            DB::beginTransaction();

            $user_id = auth('api')->id() ?? Auth::id();
            DB::transaction(function () use ($payout, $user_id) {
                // Lock payout row to avoid races
                $payout = PayOut::lockForUpdate()->find($payout->id);

                // 2) Create MainTransaction for the PayOut
                $transaction = [
                    'date' => $payout->date,
                    'customer_id' => $payout->customer_id,
                    'vendor_id' => $payout->vendor_id,
                    'staff_id' => $payout->staff_id,
                    'trans_type' => 'Debit',
                    'classification' => 'Pay Out',
                    'reference_type' => 'App\Models\Finance\PayOut',
                    'reference_id' => $payout->id,
                    'amount' => $payout->amount,
                    'paid' => $payout->amount,
                ];
                
                $main_transaction = $this->finance_main_transaction_create($transaction);
                if (is_string($main_transaction)){
                    DB::rollback();
                    $this->log_user_activity('Pay Out Confirmed', $payout->id, false);
                    return $main_transaction;
                }

                $account = BranchBank::findOrFail($payout->account_id);

                $account->balance -= $account->amount;
                $account->save();
                
                $remaining = (float) $payout->amount;

                // 3) If invoice_id exists on payout (explicit target), allocate to it first
                if (!empty($payout->expense_id)) {
                    $expense = Expense::lockForUpdate()->find($payout->expense_id);
                    $total_payments = PayOutAllocation::where('expense_id', '=', $payout->expense_id)->selectRaw('ROUND(SUM(`amount`), 2) as total_payments')->first()->total_payments;

                    //echo 'Query Calculation: '.$total_payments;
                    //echo 'DB Calculation: '.$expense->computeTotalPayments();

                    if ($expense){

                        $expense_outstanding = (float) max(0, $expense->amount - $total_payments);
                        $allocAmount = min($expense_outstanding, $remaining);

                        if ($allocAmount > 0) {
                            // create allocation record joining this payout -> invoice (use PaymentAllocation table)
                            PayOutAllocation::create([
                                'expense_id' => $expense->id,
                                'amount' => $allocAmount,
                                'pay_out_id' => $payout->id,
                            ]);

                            // update invoice status
                            $newPaid = $total_payments + $allocAmount;

                            //echo "\n New Total Paid: ".$newPaid;
                            //echo "\n New Allocated Amount:".$allocAmount;
                            if ($newPaid >= $expense->amount) {
                                $expense->status = Invoice::StatusPaid;
                            } elseif ($newPaid > 0) {
                                $expense->status = Invoice::StatusPartPaid;
                            }
                            $expense->save();

                            $remaining -= $allocAmount;
                        }
                    }
                }

                // 4) If still remaining, get other unpaid / part-paid invoices for vendor and allocate oldest-first
                if ($remaining > 0) {
                    $otherExpenses = Expense::where('vendor_id', $payout->vendor_id)
                        ->whereIn('status', [Expense::StatusConfirmed, Expense::StatusPartPaid])
                        ->orderBy('due_date', 'asc')->lockForUpdate()->get();

                    foreach ($otherExpenses as $o_expense) {
                        if ($remaining <= 0) break;

                        $o_total_payments = PayOutAllocation::where('expense_id', '=', $o_expense->id)->selectRaw('ROUND(SUM(`amount`), 2) as total_payments')->first()->total_payments;
                        // compute outstanding
                        $outstanding = (float)max(0, $o_expense->amount - $o_total_payments);
                        if ($outstanding <= 0) continue;

                        $allocAmount = min($outstanding, $remaining);

                        PayOutAllocation::create([
                            'expense_id' => $o_expense->id,
                            'amount' => $allocAmount,
                            'pay_out_id' => $payout->id,
                            'created_by' => $user_id
                        ]);

                        $newPaid = $o_total_payments + $allocAmount;
                        if ($newPaid >= $expense->amount) {
                            $expense->status = Expense::StatusPaid;
                        } elseif ($newPaid > 0) {
                            $expense->status = Expense::StatusPartPaid;
                        }
                        $expense->save();

                        $remaining -= $allocAmount;
                    }
                }

                // 5) If still remaining, credit vendor account balance (vendor->account_balance or vendor->balance)
                if ($remaining > 0) {
                    $vendor = Vendor::lockForUpdate()->find($payout->vendor_id);
                    if ($vendor) {
                        $vendor->balance = ($vendor->balance ?? 0) + $remaining;
                        $vendor->save();
                    }
                }

                // 1) Mark payout as CONFIRMED
                $payout->status = PayOut::StatusConfirmed;
                $payout->confirmed_by = $user_id;
                $payout->confirmed_at = date('Y-m-d H:i:s');
                $payout->save();
            
            });

            DB::commit();
            $this->log_user_activity('Pay Out Confirmed', $id, true);
            return $payout;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Pay Out Confirmed', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_pay_out_create($data){
        DB::beginTransaction();

        try{
            //Create new Account if required
            if($data['receiving_account_id'] == -1){
                if (!empty($data['customer_id'])){
                    $receiving_account = CustomerAccount::updateOrCreate([
                        'vendor_id' => $data['vendor_id'],
                        'bank_id' => $data['receiving_account']['bank_id'],
                        'account_name' => $data['receiving_account']['account_name'],
                        'account_number' => $data['receiving_account']['account_number'],
                    ],
                    [
                        'status' => 1,
                    ]);
                }
                elseif(!empty($data['staff_id'])){
                    $receiving_account = EmployeeAccount::updateOrCreate([
                        'employee_id' => $data['employee_id'],
                        'bank_id' => $data['receiving_account']['bank_id'],
                        'account_name' => $data['receiving_account']['account_name'],
                        'account_number' => $data['receiving_account']['account_number'],
                    ],
            [
                        'status' => 1,
                    ]);
                }
                elseif(!empty($data['vendor_id'])){
                    $receiving_account = VendorAccount::updateOrcreate([
                        'vendor_id' => $data['vendor_id'],
                        'bank_id' => $data['receiving_account']['bank_id'],
                        'account_name' => $data['receiving_account']['account_name'],
                        'account_number' => $data['receiving_account']['account_number'],],
            [
                            'status' => 1,
                    ]);
                }
                $receiving_account_id = $receiving_account->id;
            }
            else{
                $receiving_account_id = $data['receiving_account_id'];
            }

            $query = PayOut::create([
                'unique_id' => $this->finance_generate_unique_id('payout'),
                'account_id' => $data['account_id'],
                'amount' => $data['amount'],
                'customer_id' => $data['customer_id'] ?? null,
                'date' => $data['date'] ?? date('Y-m-d'),
                'expense_id' => $data['expense_id'] ?? null,
                'receiving_account_id' => $receiving_account_id,
                'staff_id' => $data['staff_id'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => PayOut::StatusDraft,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Finance Expense Payout Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Expense PayOut Created', null, false);
            return $e->getMessage();
        }
    }

    public function finance_pay_out_get_all($type, $specific, $detailed, $paginated){
        $query = PayOut::query();
        
        switch($type){
            
        }

        if(is_array($specific)){
            if(isset($specific['customer_id']) && !empty($specific['customer_id'])){
                $query = PayOut::where('customer_id', '=', $specific['customer_id']);
            }
            if(isset($specific['vendor_id']) && !empty($specific['vendor_id'])){
                $query = PayOut::where('vendor_id', '=', $specific['vendor_id']);
            }
            if(isset($specific['start_date']) && !empty($specific['start_date'])){
                $query = PayOut::where('date', '>=', $specific['start_date']);
            }
            if(isset($specific['end_date']) && !empty($specific['end_date'])){
                $query = PayOut::where('date', '<=', $specific['end_date']);
            }
        }

        $query = $detailed ? $query->with(['account.bank', 'customer', 'staff', 'vendor', 'allocations', 'creator', 'updater', 'deleter']) : $query->select('id', 'unique_id', 'amount', 'date');
        $query->orderBy('date', 'ASC');
        $query = $paginated ? $query->paginate(50) : $query->get();
        return $query; 
    }

    public function finance_pay_out_get_by($type, $id, $detailed){
        try{

            $query = PayOut::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            $query = $detailed ? $query->with(['customer', 'staff', 'vendor', 'allocations', 'creater', 'updater', 'deleter']) : $query->select('id', 'unique_id', 'amount', 'date');

            $query->firstOrFail();

            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
 
    public function finance_pay_out_reverse($id){
        DB::beginTransaction();

        try{
            $payout = PayOut::with(['vendor'])->findOrFail($id);

            if ($payout->status != PayOut::StatusConfirmed) {
                return 'Only confirmed payouts can be reversed.';
            }

            $userId = auth('api')->id() ?? Auth::id();

            DB::transaction(function () use ($payout, $userId) {
                // Lock payout
                $payout = PayOut::lockForUpdate()->find($payout->id);

                // 1) Find all allocations with this payout_id
                $allocations = PayOutAllocation::where('payout_id', $payout->id)->get();

                // 2) For each allocation, reverse its effect on the invoice
                foreach ($allocations as $alloc) {
                    $expense = Expense::lockForUpdate()->find($alloc->expense_id);
                    if ($expense) {
                        // Delete the allocation row
                        // If you prefer to keep allocations for audit, mark them reversed instead of deleting:
                        // $alloc->deleted_by = $userId; $alloc->save(); $alloc->delete();
                        $alloc->delete();

                        // Re-evaluate expense status
                        $paid = $expense->computeTotalPayments();
                        if ($paid <= 0) {
                            $expense->status = Expense::StatusConfirmed;
                        } elseif ($paid < $expense->amount) {
                            $expense->status = Expense::StatusPartPaid;
                        } else {
                            $expense->status = Expense::StatusPaid;
                        }
                        $expense->save();
                    } 
                    else {
                        // allocation references missing invoice: just delete allocation
                        $alloc->delete();
                    }
                }

                $mts = MainTransaction::where('reference_type', 'PayOut')
                    ->where('reference_id', $payout->id)
                    ->first();

                $mts->deleted_by = $userId;
                $mts->deleted_at = date('Y-m-d H:i:s');

                $mts->save();

                /* 
                3) Reverse MainTransaction(s) referencing this payout
                $mts = MainTransaction::where('reference_type', 'PayOut')
                    ->where('reference_id', $payout->id)
                    ->get();

                foreach ($mts as $mt) {
                    // create reversal entry with negative amount for audit
                    MainTransaction::create([
                        'transaction_number' => MainTransaction::generateNumber(),
                        'type' => $mt->type . '_reversal',
                        'reference_type' => 'PayOutReversal',
                        'reference_id' => $payout->id,
                        'vendor_id' => $payout->vendor_id,
                        'amount' => -1 * (float) $mt->amount,
                        'account_id' => $mt->account_id,
                        'posted_at' => Carbon::now(),
                        'metadata' => json_encode(['reversed_mt_id' => $mt->id]),
                        'created_by' => $userId
                    ]);

                    // optionally mark original mt as reversed (if you have a 'reversed' flag)
                    $mt->metadata = json_encode(array_merge((array) json_decode($mt->metadata, true), ['reversed_at' => Carbon::now(), 'reversed_by' => $userId]));
                    $mt->save();
                }
                */
                // 4) If payout had leftover credited to vendor account, debit it back
                if (!empty($payout->leftover_amount) && $payout->leftover_amount > 0) {
                    $vendor = Vendor::lockForUpdate()->find($payout->vendor_id);
                    if ($vendor) {
                        $vendor->account_balance = ($vendor->account_balance ?? 0) - $payout->leftover_amount;
                        $vendor->save();
                    }

                    /* Create vendor_credit reversal main transaction
                    MainTransaction::create([
                        'transaction_number' => MainTransaction::generateNumber(),
                        'type' => 'vendor_credit_reversal',
                        'reference_type' => 'PayOut',
                        'reference_id' => $payout->id,
                        'vendor_id' => $payout->vendor_id,
                        'amount' => -1 * (float) $payout->leftover_amount,
                        'account_id' => $payout->account_id ?? null,
                        'posted_at' => Carbon::now(),
                        'metadata' => json_encode(['note' => 'reversal of leftover credited to vendor']),
                        'created_by' => $userId
                    ]);*/
                }

                // 5) Mark payout as reversed
                $payout->status = PayOut::StatusReversed;
                $payout->deleted_by = $userId;
                $payout->deleted_at = date('Y-m-d H:i:s');
                $payout->save();
            });
            DB::commit();
            $this->log_user_activity('Finance Pay Out reversed', $id, true);

            return $payout;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Finance Pay Out reversed', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_pay_out_update($data, $id){
        DB::beginTransaction();

        try{
            $query = PayOut::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();

            if($data['receiving_account_id'] == -1){
                if (!empty($data['customer_id'])){
                    $receiving_account = CustomerAccount::updateOrCreate([
                        'customer_id' => $data['customer_id'],
                        'bank_id' => $data['receiving_account']['bank_id'],
                        'account_name' => $data['receiving_account']['account_name'],
                        'account_number' => $data['receiving_account']['account_number'],
                    ],
            [
                        'status' => 1,
                    ]);
                }
                else if(!empty($data['staff_id'])){
                    $receiving_account = EmployeeAccount::updateOrCreate([
                        'employee_id' => $data['staff_id'],
                        'bank_id' => $data['receiving_account']['bank_id'],
                        'account_name' => $data['receiving_account']['account_name'],
                        'account_number' => $data['receiving_account']['account_number'],
                    ],
            [    
                        'status' => 1,
                    ]);
                }
                else if(!empty($data['vendor_id'])){
                    $receiving_account = VendorAccount::updateOrCreate([
                        'vendor_id' => $data['vendor_id'],
                        'bank_id' => $data['receiving_account']['bank_id'],
                        'account_name' => $data['receiving_account']['account_name'],
                        'account_number' => $data['receiving_account']['account_number'],
                    ],
                    [
                        'status' => 1,
                    ]);
            
                }
                $receiving_account_id = $receiving_account->id;
            }
            else{
                $receiving_account_id = $data['receiving_account_id'];
            }

            
            $query->unique_id = $data['unique_id'] ?? $query->unique_id;
            $query->account_id = $data['account_id'] ?? $query->account_id;
            $query->amount = $data['amount'] ?? $query->amount;
            $query->customer_id = $data['customer_id'] ?? $query->customer_id;
            $query->date = $data['date'] ?? $query->date;
            $query->expense_id = $data['expense_id'] ?? null ?? $query->expense_id;
            $query->receiving_account_id = $receiving_account_id ?? $query->account_id;
            $query->staff_id = $data['staff_id'] ?? null ?? $query->staff_id;
            $query->vendor_id = $data['vendor_id'] ?? null ?? $query->vendor_id;
            $query->description = $data['description'] ?? null ?? $query->description;
            $query->status = PayOut::StatusDraft ?? $query->status;

            $query->save();
            
            return $query;
        }
        catch(Exception $e){

        }
    }
}