<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\LogTrait;

use App\Models\EMR\AdmissionRequest;
use App\Models\EMR\Consultation;
use App\Models\EMR\Dialysis;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance as PatientInsurance;
use App\Models\EMR\SpecialtyDoctor;
use App\Models\EMR\Visit;
use App\Models\EMR\VisitTransaction;
use App\Models\EMR\VisitTransactionCoverage;
use App\Models\Finance\Payment;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\Transaction;
use App\Models\Insurance\ContactPerson;
use App\Models\Insurance\Plan;
use App\Models\Insurance\PlanBranch;
use App\Models\Insurance\Provider;
use App\Models\Insurance\ProviderType;
use App\Models\Operations\Branch;

use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

trait InsuranceTrait{
    use LogTrait, TransactionTrait;

    private function normalizeMoney($value): float{
        return round((float) $value, 2);
    }

    private function normalizePercent($value): float{
        return round(min(100, max(0, (float) $value)), 2);
    }

    public function insurance_claims_get_all($data){
        // Items in the query 1. Patient ID 2. Provider ID 3. Plan ID 4. Start Date 5. End Date 6. List of Visits 7. Report Type 8. Report Format
        $query = Payment::whereNotNull('plan_id');
        switch($data['report_type']){
            case 'patients':
                $patient_transactions = Transaction::whereIn('patient_id', $data['patients'])
                    ->whereDate('date', '>=', $data['start_date'])
                    ->whereDate('date', '<=', $data['end_date'])
                    ->pluck('id');
                $query = ((!is_null($data['visits'])) && (is_array($data['visits']))) ? $query->whereIn('visit_id', $data['visits']): $query; 
                $query->whereIn('transaction_id', $patient_transactions);
            break;
            case 'visits':
                $patient_transactions = Transaction::whereIn('visit_id', $data['visits'])->pluck('id');
                $query->whereIn('transaction_id', $patient_transactions);
            break;
            case 'provider_summary':
                $plans = Plan::where('provider_id', '=', $data['provider_id'])->pluck('id');
                $patient_transactions = Transaction::whereIn('plan_id', $plans)
                    ->whereDate('date', '>=', $data['start_date'])
                    ->whereDate('date', '<=', $data['end_date'])
                    ->pluck('id');
                $query->whereIn('transaction_id', $patient_transactions);
            break;
            case 'plan_summary':
                $patient_transactions = Transaction::whereIn('plan_id', $data['plan_id'])
                    ->whereDate('date', '>=', $data['start_date'])
                    ->whereDate('date', '<=', $data['end_date'])
                    ->pluck('id');
                $query->whereIn('transaction_id', $patient_transactions);
            break;
        }
        $query->with(['plan.provider', 'transaction.visit'])->orderBy('updated_by', 'DESC')->get()->groupBy('transaction.visit');
        return $query;
    }
    public function insurance_provider_create($data){
        $provider = Provider::create([
            'name' => $data['name'],
            'hmo_type_id' => $data['hmo_type_id'],
            'website' => $data['website'] ?? NULL,
            'portal' => $data['portal'] ?? NULL,
            'phone' => $data['phone'] ?? NULL,
            'description' => $data['description'] ?? NULL,
            'created_by' => Auth::id() ?? auth('api')->id(),
            'updated_by' => Auth::id() ?? auth('api')->id(),
            'status' => $data['status'] ?? 1,
        ]);
    }

    public function insurance_provider_deactivate($id){
        DB::beginTransaction();
        try{
            $query = Provider::find($id);
            
            $query->status = 0;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Deactivate', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Deactivate', false, $id);
            return $e;
        }
    }

    public function insurance_provider_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Provider::orderBy('name', 'ASC');
            break;
            case 'active':
                $query = Provider::where('status', '=', 1)->orderBy('name', 'ASC');
            break;
            case 'inactive':
                $query = Provider::where('status', '!=', 1)->orderBy('name', 'ASC');
            break;
        }
        $query = $detailed ? $query->with(['insurance_type', 'plans' ]) : $query->select('id', 'name', 'hmo_type_id');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function insurance_provider_get_by_id($id){
        return Provider::where('id', '=', $id)->with(['creator', 'insurance_type', 'plans', 'updator', ])->first();
    }

    public function insurance_provider_update($data, $id){
        DB::beginTransaction();
        try{
            $query = Provider::where('id', '=', $id)->first();

            $query->name = $data['name'];
            $query->hmo_type_id = $data['hmo_type_id'];
            $query->website = $data['website'] ?? NULL;
            $query->portal = $data['portal'] ?? NULL;
            $query->phone = $data['phone'] ?? NULL;
            $query->description = $data['description'] ?? NULL;
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Update', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Update', false, $id);
            return $e;
        }
    }

    //Provider Contacts
    public function insurance_provider_contact_create($data){
        DB::beginTransaction();
        try{
            $query = ContactPerson::create([
                'name' => $data['name'],
                'provider_id' => $data['provider_id'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'status' => $data['status'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(), 
            ]);
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Contact Create', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Contact Create', false, null);
            return $e;
        } 

    }

    public function insurance_provider_contact_delete($id){
        DB::beginTransaction();
        try{
            $query = ContactPerson::find($id);

            $query->status = 0;
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Contact Deactivate', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Contact Deactivate', false, $id);
            return $e;
        }
    }

    public function insurance_provider_contact_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = ContactPerson::where('status','=', 1)->orderBy('name', 'ASC');
            break;
            case 'all':
                $query = ContactPerson::orderBy('name', 'ASC');
            break;
            case 'inactive':
                $query = ContactPerson::where('status','!=', 1)->orderBy('name', 'ASC');
            break;
            case 'provider':
                $query =  ContactPerson::where('provider_id', '=', $specific)->orderBy('name', 'ASC');
            break;
        }
        $query = $paginated ? $query->paginate(10) : $query->get();
        return $query;
    }

    public function insurance_provider_contact_get_by_id($id){
        return ContactPerson::where('id', '=', $id)->first();
    }

    public function insurance_provider_contact_update($data, $id){
        DB::beginTransaction();
        try{
            $query = ContactPerson::find($id);
            $query->name = $data['name'];
            $query->description = $
            $query->status = $data['status'];
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Contact Update', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Contact Update', false, $id);
            return $e;
        }   
    }
    //Provicer Plan Traits
    public function insurance_provider_plan_create($data){
        DB::beginTransaction();
        try{
            $query = Plan::create([
                'name' => $data['name'],
                'provider_id' => $data['provider_id'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Type Create', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Type Create', false, null);
            return $e;
        } 

    }

    public function insurance_provider_plan_delete($id){
        DB::beginTransaction();
        try{
            $query = Plan::find($id);

            $query->status = 0;
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Plan Deactivate', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Plan Deactivate', false, $id);
            return $e;
        }
    }

    public function insurance_provider_plan_get_all($type, $specific, $detailed, $paginated, $page){
        $quest = Provider::where('status', '=', 1)->pluck('id');
        $query = Plan::query();
        /*switch($type){
            case 'active':
                $query = $query->where('status','=', 1)->whereIn('provider_id', $quest)->orderBy('name', 'ASC');
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'branch':
                echo request()->cookie('branch');
                $branch = PlanBranch::where('branch_id', '=', request()->cookie('branch'))->where('status', '=', 1)->pluck('plan_id');
                $query = $query->where('status','=', 1)
                ->whereIn('provider_id', $quest)->orderBy('name', 'ASC')
                ->whereIn('id', $branch)->orderBy('name', 'ASC');
            break;
            case 'inactive':
                $query = $query->where('status','!=', 1)->orWhereIn('provider_id', $quest)->orderBy('name', 'ASC');
            break;
            case 'provider':
                $query = $query->where('provider_id', '=', $specific)->where('status','=', 1)->orderBy('name', 'ASC');
            break;
        }*/
        
        $query = $detailed ? $query->with(['creator', 'deleter', 'provider.provider_type', 'updater']) : $query->select('id', 'name', 'provider_id');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(10) : $query->get();
        return $query;
    }

    public function insurance_provider_plan_get_by_id($id){
        return  Plan::where('id', '=', $id)->with(['provider', 'price_lists.branch', 'patients', 'branches'])->first();
    }

    public function insurance_provider_plan_update($data, $id){
        DB::beginTransaction();
        try{
            $query = Plan::find($id);
            $query->name = $data['name'];
            $query->provider_id = $data['provider_id'];
            $query->description = $data['description'];
            $query->status = $data['status'];
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Plan Update', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Plan Update', false, $id);
            return $e;
        }   
    }

    // Provider Plan Branch Traits
    public function insurance_provider_plan_branch_create($data){
        DB::beginTransaction();
        try{
            $query = PlanBranch::create([
                'plan_id' => $data['plan_id'],
                'branch_id' => $data['branch_id'],
                'price_list_id' => $data['price_list_id'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Plan Create', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Plan Create', false, null);
            return $e;
        } 

    }

    public function insurance_provider_plan_branch_delete($id){
        DB::beginTransaction();
        try{
            $query = PlanBranch::find($id);

            $query->status = 0;
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Plan Branch Deactivate', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Plan Branch Deactivate', false, $id);
            return $e;
        }
    }

    public function insurance_provider_plan_branch_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = PlanBranch::where('status','=', 1)->orderBy('name', 'ASC');
            break;
            case 'all':
                $query = PlanBranch::orderBy('name', 'ASC');
            break;
            case 'inactive':
                $query = PlanBranch::where('status','!=', 1)->orderBy('name', 'ASC');
            break;
            case 'plan':
                $query = PlanBranch::where('plan_id', '=', $specific)->where('status','=', 1);
            break;
        }
        $query = $detailed ? $query->with(['plan', 'branch', 'price_list']) : $query->select('id', 'plan_id', 'branch_id', 'price_list_id'); 
        $query = $paginated ? $query->paginate(10) : $query->get();
        return $query;
    }

    public function insurance_provider_plan_branch_get_by_id($id){
        return  PlanBranch::where('id', '=', $id)->with(['plan.provider', 'price_lists', 'patients'])->first();
    }

    public function insurance_provider_plan_branch_update($data, $id){
        DB::beginTransaction();
        try{
            $query = PlanBranch::find($id);
            $query->plan_id = $data['plan_id'];
            $query->branch_id = $data['branch_id'];
            $query->price_list_id = $data['price_list_id'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Plan Branch Update', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Plan Branch Update', false, $id);
            return $e;
        }   
    }

    // Provider Type Traits
    public function insurance_provider_type_create($data){
        DB::beginTransaction();
        try{
            $query = ProviderType::create([
                'name' => $data['name'],
                'status' => $data['status'] ?? 1,
            ]);
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Type Create', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Type Create', false, null);
            return $e;
        } 
    }

    public function insurance_provider_type_delete($id){
        DB::beginTransaction();
        try{
            $query = ProviderType::find($id);

            $query->status = 0;
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Type Deactivate', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Type Deactivate', false, $id);
            return $e;
        }
    }

    public function insurance_provider_type_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = ProviderType ::where('status','=', 1)->orderBy('name', 'ASC');
            break;
            case 'all':
                $query = ProviderType ::orderBy('name', 'ASC');
            break;
            case 'inactive':
                $query = ProviderType ::where('status','!=', 1)->orderBy('name', 'ASC');
            break;
        }
        $query = $detailed ? $query->with(['plans.provider', 'providers']) : $query;
        $query = $paginated ? $query->paginate(10) : $query->get();
        return $query;
    }

    public function insurance_provider_type_get_by_id($id){
        try{
            return ProviderType::where('id', '=', $id)->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function insurance_provider_type_update($data, $id){
        DB::beginTransaction();
        try{
            $query = ProviderType::find($id);
            $query->name = $data['name'];
            $query->status = $data['status'];
            $query->save();

            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Type Update', true, $query->id);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity(auth('api')->user() ?? Auth::user(), 'Insurance Provider Type Update', false, $id);
            return $e;
        }   
    }

    //Insurance Transactions
    public function insurance_transaction_payment($data, $type){
        switch ($type){
            case 'uncovered':
                //Change the transaction Detail
                $transaction = Transaction::where('id', '=', $data['transaction_id'])->first();
                $transaction->item_unit_price = $data['item_unit_price'];
                $transaction->item_total = $data['item_unit_price'] * $transaction->item_qty;
                $transaction->updated_by = Auth::id() ?? auth('api')->id();
                $transaction->paid_by = 3;
                $transaction->save();

                $price_list_item = PriceListItem::where('item_id', '=', $transaction->item_id);
                $payment = $this->finance_payment_create($transaction->id, $data['amount'], $price_list_item, 2, $data['plan_id'], $data['auth_code'], $data['auth_channel'], $data['auth_description'] );
            break;
        }
    }
    public function insurance_transaction_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'auth':
                $query = Transaction::where('paid_by', '!=', 1)->where('status', '=', 0);
            break;
            case 'unconfirmed':
                $query = Transaction::where('paid_by', '!=', 1)->where('status', '=', 0);
            break;
            case 'uncovered':
                $visits = Visit::where('status', '<=', 1)->where('branch_id', '=', request()->cookie('current_branch'))->whereNotNull('care_id')->pluck('id');
                $query = Transaction::whereIn('visit_id', $visits);
            break;
        }
        
        $query = $detailed ? $query->with(['service_type', 'visit.patient.user', 'item', 'patient.insurances', 'payments']) : $query;
        $query = $query->orderBy('status', 'ASC')->orderBy('created_at', 'DESC');
        $query = $paginated ? $query->paginate(30) : $query->get();

        return $query;
    }


    public function insurance_transaction_coverage_auto_create($plan_id, $transaction_id){
        DB::beginTransaction();

        try{
            $visit_transaction = VisitTransaction::find($transaction_id);
            $price_list_item = PriceListItem::where('item_id', '=', $visit_transaction->item_id)->where('plan_id', '=', $plan_id)->firstOrFail();
            $plan = Plan::where('id', '=', $plan_id)->with(['provider'])->firstOrFail();
            if (VisitTransactionCoverage::where('visit_transaction_id', $transaction_id)->exists()) {
                return ('Coverage already exists for this transaction.');
            }

            $unit_price = $this->normalizeMoney($price_list_item->price);
            $unit_coverage = $this->normalizeMoney($price_list_item->coverage ?? 0);
            $covered_amount = $price_list_item->covered ? $this->normalizeMoney(min($unit_coverage, $unit_price) * $visit_transaction->quantity) : 0;

            $patient_payable = max(0, $this->normalizeMoney($visit_transaction->amount - $covered_amount));

            $coverage = VisitTransactionCoverage::create([
                'visit_transaction_id' => $transaction_id, 
                'provider_id' => $plan->provider->id ?? null, 
                'plan_id' => $plan_id,
                'authorization_code' => ($price_list_item->covered && !($price_list_item->requires_code)) ? 'Auto Cleared': null, 
                'covered_amount' => $this->normalizeMoney($covered_amount), 
                'patient_payable' => $this->normalizeMoney($patient_payable),
                'coverage_percent' => null,
                'approval_status' => ($price_list_item->covered && !($price_list_item->requires_code)) ? VisitTransactionCoverage::ApprovalApproved : VisitTransactionCoverage::ApprovalPending, 
                'claim_status' => VisitTransactionCoverage::ClaimOpen, 
                'notes' => null,
            ]);

            DB::commit();
            return $coverage;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function insurance_transaction_coverage_create($data){
        DB::beginTransaction();

        try{
            $visit_transaction = VisitTransaction::find($data['transaction_id']);
            if (VisitTransactionCoverage::where('visit_transaction_id', $data['transaction_id'])->exists()) {
                return ('Coverage already exists for this transaction.');
            }
            $price_list_item = PriceListItem::where('item_id', '=', $visit_transaction->item_id)->where('plan_id', '=', $data['plan_id'])->firstOrFail();
            $plan = Plan::where('id', '=', $data['plan_id'])->with(['provider'])->firstOrFail();

            $transaction_amount = $this->normalizeMoney($visit_transaction->amount);

            $coverage_percent = isset($data['coverage_percent']) ? $this->normalizePercent($data['coverage_percent']) : $this->normalizePercent(($data['covered_amount'] / max(1, $transaction_amount)) * 100);

            $covered_amount = isset($data['covered_amount']) ? $this->normalizeMoney($data['covered_amount']) : $this->normalizeMoney(($coverage_percent / 100) * $transaction_amount);

            $covered_amount = min($covered_amount, $transaction_amount);

            $patient_payable = $this->normalizeMoney(min(($transaction_amount - $covered_amount), 0));

            $coverage = VisitTransactionCoverage::create([
                'visit_transaction_id' => $data['transaction_id'], 
                'provider_id' => $plan->provider->id ?? null, 
                'plan_id' => $data['plan_id'],
                'authorization_code' => $data['authorization_code'], 
                'covered_amount' => $covered_amount, 
                'patient_payable' => $patient_payable,
                'coverage_percent' => $coverage_percent,
                'approval_status' => VisitTransactionCoverage::ApprovalApproved, 
                'claim_status' => VisitTransactionCoverage::ClaimOpen, 
                'notes' => null,
            ]);

            DB::commit();
            return $coverage;
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
}