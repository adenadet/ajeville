<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\UMS\UserTrait;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Contact as PatientContact;
use App\Models\EMR\Patient\Insurance as PatientInsurance;
use App\Models\EMR\Patient\Ledger as PatientLedger;
use App\Models\EMR\Visit;
use App\Models\EMR\VisitPaymentAllocation;
use App\Models\EMR\VisitTransaction;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait PatientTrait{

    use LogTrait, VisitTransactionTrait, UserTrait;
    /*
    ---------------------------------------------------------------------------------------------------
    Patient Basics
    ---------------------------------------------------------------------------------------------------
    */
    
    public function emr_patient_create($data){
        DB::beginTransaction();
        try{
            $user = $this->ums_user_create($data);

            $item_id = isset($data['patient_type']) ? $data['patient_type'] : 730; 
            $reg_type = isset($data['reg_type']) ? $data['reg_type'] : Patient::TypeTemp;

            $patient = Patient::create([
                'user_id' =>$user->id,
                'balance' =>0.00,
                'credit_limit' =>0.00,
                'patient_type' =>$reg_type,
                'unique_id' => config('app.short_code').'-'.date('YmdHis'),
                'old_emr_numbers' =>$data['old_emr_numbers'] ?? NULL,
                'blood_group' =>$data['blood_group'] ?? NULL,
                'genotype' =>$data['genotype'] ?? NULL,
                'occupation' =>$data['occupation'] ?? NULL,
                'referral_type_id' =>$data['referral_type_id'] ?? NULL,
                'referral_details' =>$data['referral_details'] ?? NULL,
                'other_details' =>$data['other_details'] ?? NULL,
                'created_by' =>Auth::id() ?? auth('api')->id(),
                'updated_by' =>Auth::id() ?? auth('api')->id(),
            ]);
            if (!empty($data['contacts']) && count($data['contacts']) != 0){
                foreach($data['contacts'] as $contact){$this->emr_patient_contact_create($patient->id, $contact);}
            }
            if (!empty($data['insurances']) && count($data['insurances']) != 0){
                foreach($data['insurances'] as $insurance){$this->emr_patient_insurance_create($patient->id, $insurance);}
            }
            if (!empty($data['contacts']) && count($data['nok']) != 0){
                //$data['nok']['user_id'] = $user->id;
                $this->ums_user_next_of_kin_create($data['nok'], $user->id);
            }
            //Create a Transaction For Registration
            //$this->emr_visit_transaction_create($item_id, $patient->id, 1, true, null);
            //Log This Activity
            $this->log_user_activity('EMR Patient Create', $patient->id, true);
            DB::commit();
            return $patient;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Patient Create', NULL, false);
            return $e->getMessage();
        }
    }

    public function emr_patient_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Patient::query();
        switch ($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                //$query = $query;
            break;
            case 'in_visit':
                $visit = Visit::where('status', '>=', 10)->where('status', '<', 20)->pluck('patient_id');
                $query = Patient::where('status', '=', 10)->whereIn('id', $visit);
            break;
            case 'name':
                $users = User::where('first_name', 'like', '%'.$specific['name'].'%')->orWhere('middle_name', 'like', '%'.$specific['name'].'%')->orWhere('last_name', 'like', '%'.$specific['name'].'%')->pluck('id');
                $query = Patient::whereIn('user_id', $users)->orWhere('unique_id', 'like', '%'.$specific['name'].'%');
            break;
        }

        $query = $detailed ? $query->has('user')->with(['insurances', 'user', 'contacts']) : $query->has('user')->with(['user']);

        $query = $paginated ? $query->paginate(100) : $query->get();

        return $query;
    }

    public function emr_patient_get_by_id($type, $id, $detailed){
        try{
            $query = Patient::where('unique_id', '=', $id)->orWhere('id', '=', $id);
            $query = $detailed ? $query->with(['user.next_of_kin', 'allergies', 'contacts', 'insurances.plan.provider', 'transactions.service_type']) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_patient_update($data, $id){
        DB::beginTransaction();
        try{
            $patient = Patient::find( $id);
            
            $patient->title             = $data['title'];
            $patient->nationality_id    = $data['nationality_id'];
            $patient->passport_no       = $data['passport_no'];
            $patient->updated_by        = auth('api')->id();
            
            $patient->save();
            $user = User::where('id', '=', $patient->user_id)->first();
            $image_url = $currentPhoto = $user->image;
            if (($data['image'] != $currentPhoto) && ($data['image'] != '')){
                $image_url = $this->save_image($data['image'], $user->id, $image_url);
            }

            $user->last_name            = $data['last_name'] ?? $user->last_name;
            $user->first_name           = $data['first_name'];
            $user->middle_name          = $data['middle_name'];
            $user->dob                  = $data['dob'];
            $user->sex                  = $data['sex'];
            $user->image                = $image_url;
            $user->email                = $data['email'];
            $user->phone                = $data['phone'];
            $user->alt_phone            = $data['alt_phone'];
            $user->street               = $data['street'];
            $user->street2              = $data['street2'];
            $user->city                 = $data['city'];
            $user->state_id             = $data['state_id'];
            $user->area_id              = $data['area_id'];
            $user->updated_by           = auth('api')->id();
            $user->save();
            
            $patient->save();
            $this->log_user_activity('EMR Patient Update', $id, false);
            DB::commit();

            return $patient;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Patient Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    ---------------------------------------------------------------------------------------------------
    Patient Contact Basics
    ---------------------------------------------------------------------------------------------------
    */

    public function emr_patient_contact_create($patient_id, $contact){
        DB::beginTransaction();
        try{

            $query = PatientContact::create([
                'patient_id' => $patient_id,
                'name' => $contact['name'],
                'address' => $contact['address'],
                'email_address' => $contact['email'],
                'phone' => $contact['phone'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            
            $this->log_user_activity('EMR Patient Insurance Create', true, $patient_id);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Patient Insurance Create', false, $patient_id);
        }
    }

    public function emr_patient_contact_deactivate($id){
        DB::beginTransaction();
        try{

            $query = PatientContact::find($id);
            
            $query->status = 0;
            $query->updated_by = AUth::id() ?? auth('api')->id();
            $query->save();
            
            $this->log_user_activity('EMR Patient Insurance Deactiviate', true, $id);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Patient Insurance Deactiviate', false, $id);
        }
    }

    public function emr_patient_contact_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'patient_active':
                $query = PatientContact::where('patient_id', '=', $specific)->whereDate('expiry_date', '>', date('Y-m-d'))->where('status', '=', 1);
            break;
            case 'patient_all':
                $query = $query = PatientContact::where('patient_id', '=', $specific);
            break;
            case 'patient_inactive':
                $query = $query = PatientContact::where('patient_id', '=', $specific)->whereDate('expiry_date', '<=', date('Y-m-d'))->where('status', '!=', 1);
            break;
        }

        $query = $detailed ? $query->with(['plan.provider']) : $query;
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function emr_patient_contact_update($patient_id, $insurance, $id){
        DB::beginTransaction();

        try{
            $query = PatientContact::find($id);

            $query->patient_id = $patient_id;
            $query->provider_id = $insurance['provider_id'];
            $query->plan_id = $insurance['plan_id'];
            $query->enrollee_id = $insurance['enrollee_id'];
            $query->status = 1;
            $query->expiry_date = $insurance['expiry_date'] ?? null;
            $query->other_details = $insurance['other_details'] ?? null;
            $query->created_by = AUth::id() ?? auth('api')->id();
            $query->updated_by = AUth::id() ?? auth('api')->id();
            
            $query->save(); 
            
            $this->log_user_activity('EMR Patient Insurance Update', true, $id);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Patient Insurance Update', false, $id);
        }
    }

    /*
    ---------------------------------------------------------------------------------------------------
    Patient Insurance Basics
    ---------------------------------------------------------------------------------------------------
    */
    public function emr_patient_insurance_create($patient_id, $insurance){
        DB::beginTransaction();
        try{

            $query = PatientInsurance::create([
                'patient_id' => $patient_id,
                'provider_id' => $insurance['provider']['id'],
                'plan_id' => $insurance['id'],
                'enrollee_id' => $insurance['enrollee_id'] ?? null,
                'status' => 1,
                'expiry_date' => $insurance['expiry_date'] ?? null,
                'other_details' => $insurance['other_details'] ?? null,
                'created_by' => AUth::id() ?? auth('api')->id(),
                'updated_by' => AUth::id() ?? auth('api')->id(),
            ]);

            
            $this->log_user_activity('EMR Patient Insurance Create', true, $patient_id);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Patient Insurance Create', false, $patient_id);
        }
    }

    public function emr_patient_insurance_deactivate($id){
        DB::beginTransaction();
        try{

            $query = PatientInsurance::find($id);
            
            $query->status = 0;
            $query->updated_by = AUth::id() ?? auth('api')->id();
            $query->save();
            
            $this->log_user_activity('EMR Patient Insurance Deactiviate', true, $id);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Patient Insurance Deactiviate', false, $id);
        }
    }

    public function emr_patient_insurance_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'patient_active':
                $query = PatientInsurance::where('patient_id', '=', $specific)->whereDate('expiry_date', '>', date('Y-m-d'))->where('status', '=', 1);
            break;
            case 'patient_all':
                $query = $query = PatientInsurance::where('patient_id', '=', $specific);
            break;
            case 'patient_inactive':
                $query = $query = PatientInsurance::where('patient_id', '=', $specific)->whereDate('expiry_date', '<=', date('Y-m-d'))->where('status', '!=', 1);
            break;
        }

        $query = $detailed ? $query->with(['plan.provider']) : $query;
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function emr_patient_insurance_update($patient_id, $insurance, $id){
        DB::beginTransaction();

        try{
            $query = PatientInsurance::find($id);

            $query->patient_id = $patient_id;
            $query->provider_id = $insurance['provider_id'];
            $query->plan_id = $insurance['plan_id'];
            $query->enrollee_id = $insurance['enrollee_id'];
            $query->status = 1;
            $query->expiry_date = $insurance['expiry_date'] ?? null;
            $query->other_details = $insurance['other_details'] ?? null;
            $query->created_by = AUth::id() ?? auth('api')->id();
            $query->updated_by = AUth::id() ?? auth('api')->id();
            
            $query->save();
  
            
            $this->log_user_activity('EMR Patient Insurance Update', true, $id);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Patient Insurance Update', false, $id);
        }
    }

    public function patient_get_all_patients($type, $detailed, $paginated){

    }
}