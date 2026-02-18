<?php

namespace App\Http\Traits\EMR;

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
use App\Services\EMR\LedgerService as EMRLedgerService;
use App\Services\EMR\TransactionService;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait VisitTransactionTrait{
    use LogTrait, InsuranceTrait;

    public function emr_visit_transaction_created($item_id, $patient_id, $quantity = 1, $auto_debit = false, $visit_id = NULL){
        DB::beginTransaction();
        try{
            $item = Item::select('name', 'billable', 'type_id', 'service_type_id')->where('id', '=', $item_id)->first();
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

                    //$this->emr_visit_patient_debit($transaction->id, $credit_price_list_item->coverage, $credit_price_list_item);
                }
                else{
                    $transaction = VisitTransaction::create([
                        'date'              => date('Y-m-d'),
                        'customer_id'       => $patient->user->customer,
                        'visit_id'          => $visit_id,
                        'patient_id'        => $patient_id,
                        'item_id'           => $item_id,
                        'service_type_id'   => $item->service_type_id,
                        'item_name'         => $item->name,
                        'item_qty'          => $quantity ?? 1,
                        'item_unit_cost'    => $cash_price_list_item->price,
                        'item_total'        => ($cash_price_list_item->price * ($quantity ?? 1)),
                        'discount'          => 0.00,
                        'description'       => '',
                        'status'            => $auto_debit ? VisitTransaction::StatusCompleted : VisitTransaction::StatusPending,
                        'service_status'    => $auto_debit ? 1 : 0,
                        'paid_by'           => 1,
                        'care_id'           => $visit->plan_id ?? NULL,
                        'created_by'        => auth('api')->id() ?? Auth::id(),
                        'updated_by'        => auth('api')->id() ?? Auth::id(),
                    ]);
                    if ($auto_debit){
                        //$this->emr_visit_patient_debit($transaction->id, $cash_price_list_item->price, $cash_price_list_item);
                        $patient->balance = $patient->balance - $cash_price_list_item->price;
                        $patient->save();
                    }
                }
            }
            $this->log_user_activity('Finance Transaction Create', true, $transaction->id);
            DB::commit();
            return $transaction;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Finance Transaction Create', false, null);
        }  
    }

    public function emr_visit_transaction_create($item_id, $patient_id, $quantity = 1, $auto_debit = false, $visit_id = null) {
        return DB::transaction(function () use ($item_id, $patient_id, $quantity, $auto_debit, $visit_id) {
            $item = Item::select('id', 'name', 'service_type_id', 'billable')->findOrFail($item_id);
            if (!$item->billable) {throw new Exception('Item is not billable.');}
            $patient = Patient::findOrFail($patient_id);
            $visit = $visit_id ? Visit::with('branch')->findOrFail($visit_id) : null;
            $branch_id = $visit->branch_id ?? (request()->cookie('current_branch') ?? 1);
            $branch = Branch::findOrFail($branch_id);
            
            $cashPriceItem = PriceListItem::where('item_id', '=', $item_id)->where('price_list_id', '=', $branch->price_list_id)->firstOrFail();
            
            $unitPrice     = $cashPriceItem->price;
            $paidBy        = VisitTransaction::PaidByPatient ?? 1;
            $coverageData  = null;

            if ($visit && $visit->plan_id) {

                $planPriceList = BranchPlanPriceList::where('plan_id', $visit->plan_id)->where('branch_id', $branch_id)->first();

                if ($planPriceList) {
                    $planPriceItem = PriceListItem::where('item_id', $item_id)->where('price_list_id', $planPriceList->price_list_id)->first();

                    if ($planPriceItem && $planPriceItem->covered === 'yes') {
                        $coveredAmount = $planPriceItem->coverage * $quantity;
                        $totalAmount   = $planPriceItem->price * $quantity;
                        $patientPay    = max(0, $totalAmount - $coveredAmount);
                        $unitPrice = $planPriceItem->price;
                        $paidBy    = $patientPay > 0 ? 3 : 2;
                        $coverageData = [
                            'plan_id'           => $visit->plan_id,
                            'covered_amount'    => $coveredAmount,
                            'patient_payable'   => $patientPay,
                            'coverage_percent'  => $planPriceItem->coverage_percent ?? null,
                            'approval_status'   => VisitTransactionCoverage::ApprovalPending,
                            'claim_status'      => VisitTransactionCoverage::ClaimOpen,
                        ];
                    }
                }
            }

            $transaction = VisitTransaction::create([
                'date'            => now()->toDateString(),
                'visit_id'        => $visit_id,
                'patient_id'      => $patient_id,
                'item_id'         => $item_id,
                'service_type_id' => $item->service_type_id,
                'item_name'       => $item->name,
                'item_qty'        => $quantity,
                'item_unit_cost'  => $unitPrice,
                'item_total'      => $unitPrice * $quantity,
                'status'          => $auto_debit ? VisitTransaction::StatusCompleted : VisitTransaction::StatusPending,
                'service_status'  => $auto_debit ? 1 : 0,
                'paid_by'         => $paidBy,
                'care_id'         => $visit->plan_id ?? null,
                'created_by'      => auth('api')->id(),
                'updated_by'      => auth('api')->id(),
            ]);

            if ($coverageData) {
                $transaction->coverage()->create(array_merge($coverageData, ['visit_transaction_id' => $transaction->id,]));
            }

            $this->log_user_activity('Finance Transaction Create', $transaction->id, true);
            return $transaction;
        });
    }


    public function emr_visit_transaction_create_multiple($data){
        DB::beginTransaction();

        try{   
            $visit_transactions = [];      
            foreach($data['items'] as $service){
                $transaction = new TransactionService();
                $visit_transaction = $transaction->create_transaction([
                    'consultation_id' => $data['consultation_id'] ?? null,
                    'date' => $data['date'] ?? date('Y-m-d'),
                    'item_id' => $service['item_id'], 
                    'patient_id' => $data['patient_id'],
                    'quantity' => $data['quantity'] ?? 1,
                    'request_type_id' => $data['request_type_id'] ?? 0,
                    'special' => $data['special'] ?? 0,
                    'type_id' => $data['request_type_id'] ?? null,
                    'visit_id' => $data['visit_id'] ?? null,
                ]);

                if(is_string($visit_transaction)){
                    throw new Exception('Something went wrong');
                }
                array_push($visit_transactions, $visit_transaction);
            }

            DB::commit();
            return $visit_transactions;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function emr_visit_transaction_deactivate($id){
        DB::beginTransaction();
        try{
            $query = VisitTransaction::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();

            if (!is_null($query->performed_at)){
                DB::rollBack();
                return "Transaction has already been performed";
            }

            $query->status = VisitTransaction::StatusCancelled;
            $query->deleted_by = Auth::id() ?? auth('api')->id();
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->deleted_at = date('Y-m-d H:i:s');

            $query->save();
            DB::commit();

            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function emr_visit_transaction_get_all($type, $specific, $detailed, $paginated){
        $query = VisitTransaction::query();

        if (is_array($specific)){
            if (!empty($specific['patient_id'])){
                $query = $query->where('patient_id', '=', $specific['patient_id']);
            }
            if (!empty($specific['visit_id'])){
                $query = $query->where('visit_id', '=', $specific['visit_id']);
            }
            if (!empty($specific['start_date'])){
                $query = $query->whereDate('date', '>=', $specific['start_date']);
            }
            if (!empty($specific['end_date'])){
                $query = $query->whereDate('date', '<=', $specific['end_date']);
            }
        }

        $query = $detailed ? $query->with(['coverage', 'creator', 'item', 'patient.user', 'performer', 'visit']) : $query->select('id', 'unique_id')->with(['item',]);
        $query->latest();
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function emr_visit_transaction_get_by($id, $detailed){
        try{
            $query = VisitTransaction::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            $query = $detailed ? $query->with(['coverage', 'creator', 'item', 'patient.user', 'performer', 'visit']) : $query->select('id', 'unique_id')->with(['item',]);
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_visit_transaction_payment($id, $forced){
        DB::beginTransaction();
        try{
            $query = VisitTransaction::where('id', '=', $id)->orWhere('unique_id', '=', $id)->with(['coverage'])->firstOrFail();
            $payable = $query->coverage ? $query->coverage->patient_payable : $query->item_total; 
            $patient = Patient::findOrFail($query->patient_id);
            $ledger = new EMRLedgerService();
            if ($query->status == VisitTransaction::StatusCompleted){
                throw new Exception('Transaction has already been paid');
            }

            if (($patient->balance + $payable) > 0) {
                if ($forced){
                    $new_row = $ledger->createLedgerEntry($patient->id, $query->visit_id, $id, null, 'debit', 'debit');
                }
                else{
                    throw new Exception('Insufficient Balance');
                }
            }

            $coverage = VisitTransactionCoverage::where('visit_transaction_id', '=', $id)->where('approval_status', '=', VisitTransactionCoverage::ApprovalApproved)->first();

            if($query->item_total <= ($coverage->covered_amount ?? 0 + $new_row->amount)){
                $query->status = VisitTransaction::StatusCompleted;
                $query->updated_by = auth('api')->id() ?? Auth::id();

                $query->save();
            }

        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_visit_transaction_update($data, $id){
        DB::beginTransaction();
        try{
            $query = VisitTransaction::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();

            $query->date = $data['date'] ?? $query->date;
            $query->visit_id = $data['visit_id'] ?? $query->visit_id;
            $query->patient_id = $data['patient_id'] ?? $query->patient_id;
            $query->service_type_id = $data['service_type_id'] ?? $query->service_type_id;
            $query->item_name = $data['item_name'] ?? $query->item_name;
            $query->item_id = $data['item_id'] ?? $query->item_id;
            $query->serviceable_type = $data['serviceable_type'] ?? $query->serviceable_type;
            $query->serviceable_id = $data['serviceable_id'] ?? $query->serviceable_id;
            $query->quantity = $data['quantity'] ?? $query->quantity;
            $query->unit_price = $data['unit_price'] ?? $query->unit_price;
            $query->amount =  $data['amount'] ?? $data['unit_price'] * $data['quantity'] ?? $query->amount;
            $query->billable = $data['billable'] ?? $query->billable;
            $query->status = $data['status'] ?? $query->status;
            $query->performed_at = $data['performed_at'] ?? $query->performed_at;
            $query->performed_by = $data['performed_by'] ?? $query->performed_by;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->deleted_by = $data['deleted_by'] ?? null;
            $query->deleted_at = $data['deleted_at'] ?? null;

            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function emr_visit_payment_create($data){
        return DB::transaction(function () use ($data) {
            $userId = auth('api')->id() ?? Auth::id();
            $payment = VisitPayment::create([
                'visit_id'       => $data['visit_id'],
                'patient_id'     => $data['patient_id'],
                'amount'         => $data['amount'],
                'payment_method' => $data['payment_method'] ?? VisitPayment::DefaultPaymentMethod,
                'reference'      => $data['reference'] ?? null,
                'received_by'    => $data['received_by'] ?? $userId,
                'received_at'    => $data['received_at'] ?? now(),
                'status'         => $data['status'] ?? VisitPayment::StatusReceived,
                'notes'          => $data['notes'] ?? null,
                'created_by'     => $userId,
                'updated_by'     => $userId,
            ]);

            //Add to Ledger
            $ledger = new EMRLedgerService();
            $payment_ledger = $ledger->createLedgerEntry($data['patient_id'], $data['visit_id'], null, $payment->id, 'credit', 'credit');

            return $payment->fresh(['allocations']);
        });
    }


    public function emr_visit_payment_deactivate($id){
        return DB::transaction(function () use ($id) {
            $userId = auth('api')->id() ?? Auth::id();
            $payment = VisitPayment::with('allocations')->lockForUpdate()->findOrFail($id);

            if ($payment->status == VisitPayment::StatusReversed) {throw new Exception('Payment already reversed.');}

            $patient = Patient::lockForUpdate()->findOrFail($payment->patient_id);

            $ledger = new EMRLedgerService();
            $payment_ledger = $ledger->createLedgerEntry($patient->id, $payment->visit_id, null, $payment->id, 'reversal', 'debit');
            
            $payment->status = VisitPayment::StatusReversed;
            $payment->updated_by = $userId;
            $payment->save();

            return $payment;
        });
    }


    public function emr_visit_payment_get_all($type, $specific, $detailed, $paginated){
        $query = VisitPayment::where('branch_id', '=', request()->cookie('current_branch'));

        switch($type){
            case 'confirmed':
                $query = $query->where('status', '=', VisitPayment::StatusTransferred);
            break;
            case 'unconfirmed':
                $query = $query->where('status', '=', VisitPayment::StatusReceived);
            break;
            case 'reversed':
                $query = $query->where('status', '=', VisitPayment::StatusReversed);
            break;
        }

        if (is_array($specific)){
            if (!empty($specific['visit_id'])){
                $query = $query->where('visit_id', '=', $specific['visit_id']);
            }
        }
        $query = $detailed ? $query->with([]) : $query->select('id', 'amount', 'balance', 'patient_id');
        $query = $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
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

            $ledger = new EMRLedgerService();
            $payment_ledger = $ledger->createLedgerEntry($query->patient_id, $query->visit_id, null, $query->id, 'reversal', 'debit');

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

            $payment_ledger = $ledger->createLedgerEntry($query->patient_id, $query->visit_id, null, $query->id, 'reversal', 'debit');

            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage(); 
        }
    }
}