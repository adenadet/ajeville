<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\InsuranceTrait;
use App\Http\Traits\UMS\LogTrait;

use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance;
use App\Models\EMR\Visit;
use App\Models\EMR\VisitPayment;
use App\Models\EMR\VisitPaymentAllocation;
use App\Models\Finance\Payment;
use App\Models\EMR\VisitTransaction;
use App\Models\EMR\VisitTransactionCoverage;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait VisitTransactionTrait{
    use LogTrait, InsuranceTrait;

    public function emr_visit_transaction_create($item_id, $patient_id, $quantity, $auto_debit = false, $visit_id = NULL){
        DB::beginTransaction();
        try{
            $item = Item::select('name', 'billable', 'service_type_id')->where('id', '=', $item_id)->first();
            $patient = Patient::where('id', '=', $patient_id)->first();
            $branch = Branch::where('id', '=',  request()->cookie('current_branch') ?? 1)->first();
            
            if($item->billable){
                $cash_price_list_item = PriceListItem::where('item_id', '=', $item_id)->where('price_list_id', '=', $branch->price_list_id)->first();

                if (is_null($visit_id)){ //Get Patient's Insurance
                    //Get Visit Insurance
                    $visit = Visit::where('id', '=', $visit_id)->first();
                    //Since each branch has a distinct price list for each plan, find the price list for the branch
                    if ($visit && !empty($visit->plan_id)){
                        $visit_price_list_id = BranchPlanPriceList::where('plan_id', '=', $visit->plan_id)->where('branch_id', '=', $visit->branch_id ?? request()->cookie('branch'))->first();
                     
                        if ($visit_price_list_id){
                            $credit_price_list_item =  PriceListItem::where('item_id', '=', $item_id)->whereIn('price_list_id','=', $visit_price_list_id)->first();
                        }
                        else{$credit_price_list_item = NULL; 
                        }
                    }
                    else{$credit_price_list_item = NULL;}
                }
                else{
                    $credit_price_list_item = NULL;
                }
                if ((!is_null($credit_price_list_item)) && ($credit_price_list_item->covered == 'yes')){
                    $transaction = VisitTransaction::create([
                        'date' => date('Y-m-d'),
                        'service_type_id' => $item->service_type_id,
                        'patient_id' => $patient_id,
                        'visit_id' => $visit_id,
                        'item_id' => $item_id,
                        'item_qty' => $quantity,
                        'item_unit_cost' => $credit_price_list_item->price,
                        'item_name' => $item->name,
                        'item_total' => $credit_price_list_item->price * $quantity,
                        'status' => 0,
                        'paid_by' => $credit_price_list_item->price == $credit_price_list_item->coverage ? 2 : 3,
                        'service_status' =>  $auto_debit ? 1 : 0, 
                        'created_by' => auth('api')->id(),
                        'updated_by' => auth('api')->id(),
                    ]);

                    $this->finance_payment_create($transaction->id, $credit_price_list_item->coverage, $credit_price_list_item);
                }
                else{
                    $transaction = VisitTransaction::create([
                        //'visit_id', 'patient_id', 'service_type', 'item_id', 'serviceable_type', 'serviceable_id', 'quantity', 'unit_price', 'amount', 'billable', 'status','performed_at', 'performed_by', 'metadata',
                        'amount' => $cash_price_list_item->price * $quantity,
                        'date' => date('Y-m-d'),
                        'service_type_id' => $item->service_type_id,
                        'patient_id' => $patient_id,
                        'item_id' => $item_id,
                        'quantity' => $quantity,
                        'unit_price' => $cash_price_list_item->price,
                        'item_name' => $item->name,
                        'status' => 0,
                        'visit_id' => $visit_id,
                        'paid_by' => 1,
                        'service_status' => $auto_debit ? 1 : 0, 
                        'created_by' => auth('api')->id(),
                        'updated_by' => auth('api')->id(),
                    ]);
                    if ($auto_debit){
                        $this->finance_payment_create($transaction->id, $cash_price_list_item->price, $cash_price_list_item);
                        $patient->balance = $patient->balance - $cash_price_list_item->price;
                        $patient->save();
                    }
                }
            }
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Finance Transaction Create', true, $transaction->id);
            DB::commit();
            return $transaction;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Finance Transaction Create', false, null);
        }  
    }

    public function emr_visit_payment_create($data){
        DB::beginTransaction();
        try{
            $query = VisitPayment::create([
                'visit_id' => $data['visit_id'],
                'patient_id' => $data['patient_id'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'] ?? VisitPayment::DefaultPaymentMethod,
                'reference' => $data['reference'] ?? null,
                'received_by' => $data['received_by'] ?? Auth::id() ?? auth('api')->id(),
                'received_at' => $data['received_at'] ?? date('Y-m-d'),
                'status' => $data['status'] ?? VisitPayment::StatusReceived,
                'notes' => $data['notes'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function emr_visit_payment_deactivate($id){
        DB::beginTransaction();
        try{
            $query = VisitPayment::find($id);
            
            $query->status = VisitPayment::StatusTransferred;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->deleted_by = Auth::id() ?? auth('api')->id();
            $query->deleted_at = date('Y-m-d H:i:s');
            
            $allocations = VisitPaymentAllocation::where('visit_payment_id', '=', $id)->get();

            foreach($allocations as $allocation){
                $allocation->updated_at = date('Y-m-d H:i:s');
                $allocation->deleted_at = date('Y-m-d H:i:s');

                $allocation->save();
            }

            $query->save();
            
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function emr_visit_payment_get_all($type, $specific, $detailed, $paginated){
        $query = VisitPayment::query();

        $query = $detailed ? $query->with([]) : $query->select('id', 'amount', 'balance', 'patient_id');
        $query = $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();
    }

    public function emr_visit_payment_get_by($type, $id, $detailed){
        try{
            $query = VisitPayment::where('id', '=', $id);

            $query = $detailed ? $query->with([]) : $query->select('id', 'amount', 'balance', 'patient_id');
            $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_visit_payment_update($data, $id){
        DB::beginTransaction();
        try{
            $query = VisitPayment::where('id', '=', $id);

            $query->visit_id = $data['visit_id'] ?? $query->visit_id;
            $query->patient_id = $data['patient_id'] ??$query->patient_id;
            $query->amount = $data['amount'] ?? $query->amount;
            $query->payment_method = $data['payment_method'] ?? $query->payment_method ??VisitPayment::DefaultPaymentMethod;
            $query->reference = $data['reference'] ?? $query->reference;
            $query->received_by = $data['received_by'] ?? $query->received_by;
            $query->received_at = $data['received_at'] ?? $query->received_at;
            $query->status = $data['status'] ?? $query->status ?? VisitPayment::StatusReceived;
            $query->notes = $data['notes'] ?? $query->notes;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage(); 
        }
    }

    public function emr_visit_payment_allocation_create($payment_id, $transactions)
    {
        DB::beginTransaction();

        try {
            $payment = VisitPayment::with('allocations')->findOrFail($payment_id);
            $payment_balance = $payment->balance;

            if ($payment_balance <= 0) {
                return 'No available balance to allocate.';
            }

            foreach ($transactions as $transaction) {
                // Only billable & not cancelled
                if (!$transaction->billable || $transaction->status == VisitTransaction::StatusCancelled) {
                    continue;
                }

                // Determine payable amount
                if ($transaction->coverage && $transaction->coverage->approval_status == VisitTransactionCoverage::ApprovalApproved) {
                    $total_payable = $transaction->coverage->patient_payable;
                } 
                else {
                    $total_payable = $transaction->amount;
                }

                // Already paid for this transaction
                $already_paid = $transaction->paymentAllocations()->sum('amount');

                $outstanding = max(0, $total_payable - $already_paid);

                if ($outstanding <= 0) {
                    continue;
                }

                // Allocate
                $amount_to_allocate = min($payment_balance, $outstanding);

                VisitPaymentAllocation::create([
                    'visit_payment_id' => $payment->id,
                    'visit_transaction_id' => $transaction->id,
                    'amount' => $amount_to_allocate,
                ]);

                $payment_balance -= $amount_to_allocate;

                if ($payment_balance <= 0) {
                    break;
                }
            }

            DB::commit();
            return $payment->fresh(['allocations']);

        } 
        catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }         
}