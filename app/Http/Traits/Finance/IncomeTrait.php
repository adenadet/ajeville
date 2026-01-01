<?php

namespace App\Http\Traits\Finance;

use App\Http\Traits\Finance\MainTransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Approvals\Action;
use App\Models\CRM\Customer;
use App\Models\Finance\BranchBank;
use App\Models\Finance\Income;
use App\Models\Finance\Invoice;
use App\Models\Finance\MainTransaction;
use App\Models\Finance\Payment;
use App\Models\Finance\PaymentAllocation;
use App\Models\Procurement\PaymentTerm;
use App\Models\Sales\Order;
use App\Services\Finance\MainService;
use App\Services\Finance\PaymentAllocationService;
use App\Services\Finance\OrderIncomeService;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait IncomeTrait{
    use MainTransactionTrait, LogTrait, SettingTrait;

    /*
    ----------------------------------------------------------------------
    Basic Incomes CRUD
    -----------------------------------------------------------------------
    */
    public function finance_income_complete($id){
        DB::beginTransaction();

        try{
            $query = Income::where('id', '=', $id)->orWhere('uuid', '=', $id)->first();

            $query->status = Income::StatusPaid;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            DB::commit();
            $this->log_user_activity('Finance Income Confirm', $id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Income Confirm', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_income_confirm($data, $id){
        DB::beginTransaction();

        try{
            $query = Income::where('id', '=', $id)->orWhere('uuid', '=', $id)->first();

            $query->status = Income::StatusConfirmed;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            DB::commit();
            $this->log_user_activity('Finance Income Confirm', $id, true);
            
            return $query;

        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Income Confirm', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_income_create($data){
        DB::beginTransaction();
        try{

            $main_service = new MainService();
            $query = Income::create([
                'unique_id' => $main_service->finance_setting_generate_unique_id('income'),
                'branch_id' => $data['branch_id'] ?? request()->cookie('current_branch'),
                'incomeable_id' => $data['incomeable_id'], //ID of the reference income
                'incomeable_type' => $data['incomeable_type'], //Invoice, Purchase Order, Asset, 
                'classification_id' => $data['classification_id'] ?? null, //Basically income type
                'amount' => $data['amount'],
                'payable' => $data['payable'] ?? $data['amount'],
                'date' => $data['date']?? date('Y-m-d'),
                'due_date' => $data['due_date'] ?? $data['date'] ?? date('Y-m-d'),
                'account_id' => $data['account_id'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'staff_id' => $data['staff_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'description' => $data['description'],
                'status' => $data['status'] ?? Income::StatusConfirmed,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            if ($query->status == Income::StatusConfirmed){
                $transaction = MainTransaction::create([
                    'date' => $query->date ?? date('Y-m-d'),
                    'payment_due_date' => $query->payment_due_date ?? date('Y-m-d'),
                    'customer_id' => $query->customer_id ?? null,
                    'vendor_id' => $query->vendor_id ?? null,
                    'staff_id' => $query->staff_id ?? null,
                    'trans_type' => 'Credit',
                    'reference_type' => 'App\Models\Finance\Income',
                    'reference_id' => $query->id,
                    'unique_id' => $this->finance_setting_generate_unique_id('transaction'),
                    'amount' => $query->amount,
                    'paid' => 0.00,
                    'payable' => $query->payable ?? $query->amount,
                    'status' => $query->status ?? 0,
                    'created_by' => Auth::id() ?? auth('api')->id(),
                    'updated_by' => Auth::id() ?? auth('api')->id(),
                ]);

                if (!empty($query->customer_id)){
                    $customer = Customer::findOrFail($query->customer_id);
                    $customer->balance += $query->amount;
                    $customer->updated_by = Auth::id() ?? auth('api')->id();
                    $customer->save();

                    $allocationService = new PaymentAllocationService();
                    $allocationService->allocateIncome($query);
                }
            }

            DB::commit();
            $this->log_user_activity('Finance Income Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Income Create', null, false);
            return $e->getMessage();
        }
    }

    public function finance_income_create_from($type, $id){
        switch ($type){
            case 'sales_order':
                $orderIncomeService = new OrderIncomeService();
                $full_data = $orderIncomeService->generateIncomeFromOrderId($id);
            break;
        }

        $income = $this->finance_income_create($full_data);
        return $income;
    }

    public function finance_income_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Income::where('id', '=', $id)->orWhere('unique_id', '=', $id)->first();

            $query->status = Income::StatusDeleted;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            DB::commit();
            $this->log_user_activity('Finance Income Deactivate', $id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Income Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_income_delete($id){
        DB::beginTransaction();

        try{
            $query = Income::where('id', '=', $id)->orWhere('uuid', '=', $id)->first();

            $query->deleted_at = date('Y-m-d H:i:s');
            $query->deleted_by = auth('api')->id() ?? Auth::id();

            $query->save();
            
            DB::commit();
            $this->log_user_activity('Finance Income Delete', $id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Income Delete', $id, false);
            return $e->getMessage();
        }
        
    }

    public function finance_income_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Income::query();

        switch($type){
            case 'confirmed':
                $query = $query->where('status', '=', Income::StatusConfirmed);
            break;
            case 'deleted':
                $query = $query->where('status', '=', Income::StatusDeleted)->withTrashed();
            break;
            case 'paid':
                $query = $query->where('status', '=', Income::StatusPaid);
            break;
            case 'unconfirmed':
                $query = $query->where('status', '=', Income::StatusUnconfirmed);
            break;
            case 'queried':
                $query = $query->where('status', '=', Income::StatusQueried);
            break;
            case 'rejected':
                $query = $query->where('status', '=', Income::StatusRejected);
            break; 
            case 'unpaid':
                $query = $query->whereIn('status', [Income::StatusQueried, Income::StatusConfirmed, Income::StatusUnconfirmed]);
            break;
        }

        if ($specific !== null){}

        $query = $detailed ? $query->with(['creator', 'deleter', 'incomeable', 'updater', 'vendor', 'customer', 'staff']) : $query->select('id', 'date', 'amount', 'vendor_id', 'date')->with(['vendor']);
        $query = $query->orderBy('due_date', 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function finance_income_get_by($type, $id, $detailed){
        try{
            $query = Income::where('id', '=', $id)->orWhere('unique_id', '=', $id);

            $query = $detailed ? $query->with(['allocations.payment', 'creator', 'customer', 'deleter', 'incomeable', 'updater', 'vendor.accounts.bank', 'staff']) : $query->select('id', 'date', 'amount', 'vendor_id', 'date')->with(['vendor']);
            
            return $query->firstOrFail();
        }
        catch(Exception $e){ 
            return $e->getMessage();
        }
    }

    public function finance_income_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Income::where('id', '=', $id)->orWhere('uuid', '=', $id)->first();

            $query->unique_id = $data['unique_id'] ?? $query->unique_id;
            $query->branch_id = $data['branch_id'] ?? $query->branch_id;
            $query->incomeable_id = $data['incomeable_id'] ?? $query->incomeable_id; //ID of the reference income
            $query->income_type = $data['income_type'] ?? $query->incomeable_type; //Invoice, Purchase Order, Asset, 
            $query->classification_id = $data['classification_id'] ?? $query->classification_id; //Basically income type
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
            $this->log_user_activity('Finance Income Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Income Update', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_payment_deposit_create($data){

        try{
            $deposit = Payment::create([
                'uuid' => $this->finance_setting_generate_unique_id('payment'),
                'date' => $data['date'] ?? date('Y-m-d'),
                'bank_id' => $data['bank_id'],
                'customer_id' => $data['customer_id'] ?? 0,
                'mode_id' => $data['mode_id'],
                'amount' => $data['amount'],
                'collected_by' => $data['collected_by'] ?? (auth('api')->id() ?? Auth::id()),
                'collected_at' => $data['collected_at'] ?? date('Y-m-d H:i:s'),
                'created_by' => (auth('api')->id() ?? Auth::id()),
                'updated_by' => (auth('api')->id() ?? Auth::id()),
            ]);

            return $deposit;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
        
    }
    /*
    ----------------------------------------------------------------------
    Payment Functions
    -----------------------------------------------------------------------
    */

    public function finance_payment_confirm($id)
    {
        DB::beginTransaction();

        try {
            $payment = Payment::lockForUpdate()->findOrFail($id);

            if ($payment->status === Payment::StatusConfirmed) {
                return 'Payout already confirmed.';
            }

            if ($payment->status === Payment::StatusReversed) {
                return 'Payout has been reversed and cannot be confirmed.';
            }

            $user_id = auth('api')->id() ?? Auth::id();

            /*
            |--------------------------------------------------
            | 1. Create Main Transaction
            |--------------------------------------------------
            */
            $main_transaction = $this->finance_main_transaction_create([
                'date' => $payment->date,
                'customer_id' => $payment->customer_id,
                'vendor_id' => $payment->vendor_id,
                'staff_id' => $payment->staff_id,
                'trans_type' => 'Credit',
                'classification' => 'Payment Income',
                'transactionable_type' => Payment::class,
                'transactionable_id' => $payment->id,
                'amount' => $payment->amount,
                'paid' => $payment->amount,
                'description' => 'Customer Deposit',
                'status' => MainTransaction::StatusPaid,
            ]);

            if (is_string($main_transaction)) {throw new Exception($main_transaction);}

            $account = BranchBank::lockForUpdate()->findOrFail($payment->bank_id);
            $account->balance += $payment->amount;
            $account->save();

            $remaining = (float) $payment->amount;

            if ($payment->customer_id) {
                $customer = Customer::lockForUpdate()->find($payment->customer_id);
                if ($customer) {
                    $customer->balance -= $remaining;
                    $customer->save();
                }
            }

            if ($remaining > 0 && $payment->income_id) {

                $income = Income::lockForUpdate()->find($payment->income_id);

                if ($income) {
                    $total_paid = (float) PaymentAllocation::where('income_id', $income->id)
                        ->sum('amount');

                    $outstanding = max(0, $income->amount - $total_paid);
                    $alloc = min($outstanding, $remaining);

                    if ($alloc > 0) {
                        PaymentAllocation::create([
                            'payment_id' => $payment->id,
                            'income_id' => $income->id,
                            'amount' => $alloc,
                            'created_by' => $user_id,
                        ]);

                        $newPaid = $total_paid + $alloc;

                        $income->status = $newPaid >= $income->amount
                            ? Income::StatusPaid
                            : Income::StatusPartPaid;

                        $income->save();

                        $remaining -= $alloc;
                    }
                }
            }

            if ($remaining > 0) {
                $otherIncomes = Income::lockForUpdate()
                    ->where('vendor_id', $payment->vendor_id)
                    ->whereIn('status', [Income::StatusConfirmed, Income::StatusPartPaid])
                    ->orderBy('due_date')
                    ->get();

                foreach ($otherIncomes as $o_income) {
                    if ($remaining <= 0) break;
                    $total_paid = (float) PaymentAllocation::where('income_id', $o_income->id)->sum('amount');
                    $outstanding = max(0, $o_income->amount - $total_paid);
                    if ($outstanding <= 0) continue;
                    $alloc = min($outstanding, $remaining);

                    PaymentAllocation::create([
                        'payment_id' => $payment->id,
                        'income_id' => $o_income->id,
                        'amount' => $alloc,
                        'created_by' => $user_id,
                    ]);

                    $newPaid = $total_paid + $alloc;
                    $o_income->status = $newPaid >= $o_income->amount ? Income::StatusPaid : Income::StatusPartPaid;
                    $o_income->save();
                    $remaining -= $alloc;
                }
            }

            $payment->status = Payment::StatusConfirmed;
            $payment->updated_by = $user_id;
            $payment->save();

            DB::commit();
            $this->log_user_activity('Finance Payment Confirmation', $id, true);
            return $payment;
        } 
        catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Finance Payment Confirmation', $id, false);
            return 'Failed to process payment: ' . $e->getMessage();
        }
    }

    public function finance_payment_create($data){
        DB::beginTransaction();

        try{
            if ($data['trans_type'] == 'sales'){
                //Create an income records for the sales if it does not already exist
                if (isset($data['reference_id']) && (empty($data['reference_id']))){
                    $order = Order::where('unique_id', '=', $data['reference_id'])->orWhere('id', '=', $data['reference_id'])
                    ->with(['order_items.item', 'order_items.fulfillments.store_item_batch.batch', 'order_items.package', 'creator', 'deleter', 'payment_term', 'store.branch', 'updater', 'customer', ])
                    ->first();

                    $payment_term = PaymentTerm::findOrFail($order->payment_term_id);
                    $payment_due_date = new DateTime($order->delivery_date);
                    $payment_due_date->modify('+'.$payment_term->days.' days');

                    $order_income = new OrderIncomeService();
                    $approved_order = $order_income->approveOrder($order, ['decision' => 'confirm', 'remark' => 'Auto Approved based on Payment', ]);
                    
                    $income = $this->finance_income_create_from('sales_order', $data['order_id']);

                    if (is_string($income)){
                        DB::rollBack();
                        $this->log_user_activity('Finance Payment Created', null, false);
                        return $income;
                    }
                } 
            }

            $main_service = new MainService();

            $query = Payment::create([
                'uuid' => $main_service->finance_setting_generate_unique_id('payment'),
                'date' => $data['date'] ?? date('Y-m-d'),
                'customer_id' => $data['customer_id'] ?? null,
                'staff_id' => $data['staff_id'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'amount' => $data['amount'],
                'mode_id' => $data['mode_id'] ?? 1,
                'bank_id' => $data['bank_id'] ?? null,
                'income_id' => $income->id ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? Payment::StatusUnconfirmed,
                'collected_by' => $data['collected_by'] ?? Auth::id() ?? auth('api')->id(),
                'collected_at' => $data['collected_at'] ?? date('Y-m-d H:i:s'),
                'confirmed_by' => $data['confirmed_by'] ?? null,
                'confirmed_at' => $data['confirmed_at'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('Finance Payment Created', $query->id, true);
            return $query;
        }
        catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Finance Payment Created', null, false);
            return $e->getMessage();
        }
    }

    public function finance_payment_deactivate($id)
    {
        DB::beginTransaction();

        try{
            $payment = Payment::findOrFail($id);
            $payment->deleted_by = auth('api')->id();
            $payment->deleted_at = date('Y-m-d H:i:s');
            $payment->save();

            DB::commit();
            $this->log_user_activity('Payment Deleted', $id, true);
            return $payment;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Payment Deleted', $id, false);
            return $e->getMessage();
        }
    }

    public function finance_payment_get_all($type, $specific, $detailed, $paginated, $page)
    {
        $query = Payment::query();

        switch($type){
            case 'confirmed':
                $query = $query->where('status', '=', Payment::StatusConfirmed);
            break;
            case 'mine':
                $query = $query->where('created_by', '=', auth('api')->id() ?? Auth::id());
            break;
            case 'period':
                $query = $query->whereDate('date', '>=', $specific['start_date'])->whereDate('date', '<=', $specific['end_date']);
            break;
            case 'unconfirmed':
                $query = $query->where('status', '=', Payment::StatusUnconfirmed);
            break;
        }

        if (is_array($specific)){
            if(isset($specific['start_date']) && !empty($specific['start_date'])){
                $query = $query->whereDate('date', '>=', $specific['start_date']);
            }
            if(isset($specific['end_date']) && !empty($specific['end_date'])){
                $query = $query->whereDate('date', '<=', $specific['end_date']);
            }
            if(isset($specific['query']) && !empty($specific['query'])){
                $search = $specific['query'];
                $customers = Customer::where('name', 'LIKE', "%$search%")->pluck('id');

                $query = $query->whereIn('customer_id', $customers);
            }
        }

        $query = $detailed ? $query->with(['account.bank', 'allocations.income', 'collector', 'customer', 'mode', ]) : $query;
        $query->latest();
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function finance_payment_get_by($id, $detailed)
    {
        try{
            $query = Payment::where('id', '=', $id)->where('unique_id', '=', $id);

            $query = $detailed ? $query->with(['account.bank', 'allocations.income', 'collector', 'customer', 'mode', ])->firstOrFail() : $query->firstOrFail();
            
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    // Update an existing payment
    public function finance_payment_update($data, $id)
    {
        DB::beginTransaction();

        try{
            $payment = Payment::findOrFail($id);

            if (!is_null($payment->confirmed_by)){
                return 'Already confirmed this payment, it can not be updated';
            }
            
            $payment->date          = $data['date'] ?? $payment->date;
            $payment->customer_id   = $data['customer_id'] ?? $payment->customer_id;
            $payment->staff_id      = $data['staff_id'] ?? $payment->staff_id;
            $payment->vendor_id     = $data['vendor_id'] ?? $payment->vendor_id;
            $payment->amount        = $data['amount'] ??$payment->amount;
            $payment->mode_id       = $data['mode_id'] ?? $payment->mode_id;
            $payment->bank_id       = $data['bank_id'] ?? $payment->bank_id;
            $payment->description   = $data['description'] ?? $payment->description;
            $payment->status        = $data['status'] ?? $payment->status;
            $payment->collected_by  = $data['collected_by'] ?? $payment->collected_by;
            $payment->collected_at  = $data['collected_at'] ?? $payment->collected_at;
            $payment->confirmed_by  = null;
            $payment->confirmed_at  = null;
            $payment->updated_by    = auth('api')->id() ?? Auth::id();

            $payment->save();
            
            DB::commit();
            $this->log_user_activity('Finance Payment Updated', $id, true);
            return $payment;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Finance Payment Updated', $id, false);
            return $e->getMessage();
        }
    } 
}