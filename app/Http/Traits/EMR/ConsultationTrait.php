<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\AppointmentTrait;
use App\Http\Traits\EMR\DialysisTrait;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\NursingTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Http\Traits\EMR\PhysiotheraphyTrait;
use App\Http\Traits\EMR\RadiologyTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\SpecialtyDoctor;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient;
use App\Models\EMR\PatientInsurance;
use App\Models\EMR\Visit;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait ConsultationTrait{

    use LogTrait, RadiologyTrait, TransactionTrait;
    private function authId(){ 
        return  Auth::id() ?? auth('api')->id();
    }
    private function emr_consultation_create_unique_id(){
        $id = config('app.short_code').'-CONS-'.strtoupper(uniqid());
        $query = Consultation::where('unique_id', '=', $id)->first();
        if (is_null($query)){
            return $id;
        }
        else{
            return $this->emr_cosultation_create_unique_id();
        }
    }

    public function emr_consultation_complete_request($data, $id){
        DB::beginTransaction();

        try{
            $query = Consultation::findOrFail($id);
            
            $query->action_plan = $data['action_plan'];
            $query->complaint = $data['complaint'];
            $query->consultant_seen_id = $this->authId();
            $query->end_time = date('Y-m-d H:i:s');
            $query->final_diagnosis = $data['final_diagnosis'] ?? null;
            $query->initial_diagnosis = $data['initial_diagnosis'];
            $query->soap_note = $data['soap_note'];
            $query->status = 4;
            $query->updated_by = $this->authId();

            $query->save();

            if (isset($data['lab_requests']) && is_array($data['lab_requests'])){
                if (count($data['lab_requests']) > 0){
                    foreach ($data['lab_requests'] as $game){
                        $this->emr_laboratory_create_request($game, null, $query);
                    }
                }
            }

            if (isset($data['rad_requests']) && is_array($data['rad_requests'])){
                if (count($data['rad_requests']) > 0){
                    foreach ($data['rad_requests'] as $game){
                        $this->emr_radiology_create_request($game, null, $query);
                    }
                }
            }

            $this->log_user_activity('Consultation done', $id, true);
            DB::commit();
            return $query;
        }
    
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Consultation done', $id, false);
            return $e->getMessage();
        }   
    }
    public function emr_consultation_create_request($data){
        DB::beginTransaction();

        try{
            $transaction = $this->finance_transaction_create($data['item_id'], $data['patient_id'], 1, true, $data['visit_id']);
            if(is_string($transaction)){ 
                DB::rollBack();
                $this->log_user_activity('Transaction create', null, false);       
                return $transaction." Could not create a transaction";
            }
            else{
                $query = Consultation::create([
                    'unique_id' => $this->emr_consultation_create_unique_id(),
                    'patient_id' => $data['patient_id'],
                    'visit_id' => $data['visit_id'],
                    'specialty_id' => $data['specialty_id'],
                    'transaction_id' => $transaction->id,
                    'consultation_type_id' => $data['consultation_type_id'],
                    'whom_to_see' => $data['whom_to_see'] ?? null,
                    'consultant_id' => $data['consultant_id'] ?? null,
                    'status' => 0,
                    'created_by' => $this->authId(),
                    'updated_by' => $this->authId(),
                ]);

                $this->log_user_activity('Transaction create', $query->unique_id, true);
                DB::commit();
                return $query;
            }
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Transaction create', null, false);
            return $e->getMessage();
        }   
    }

    public function emr_consultation_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = is_null($specific) || !isset($specific) ? Consultation::withTrashed() : Consultation::withTrashed()->whereDate('date', '>=', $specific['start_date'])->whereDate('date', '<=', $specific['end_date']);
            break;
            case 'consultant':
                if (is_array($specific)) {
                    $query = Consultation::where('consultant_id', '=', $specific['consultant_id'])->orWhere('consultant_id', '=', $specific['consultant_seen_id']);
                    if( isset($specific['start_date'])){ $query->where('date', '>=', $specific['start_date']);}
                    if(isset($specific['end_date'])){$query->where('date', '<=', $specific['end_date']);}
                    if(isset($specific['status'])){$query->where('status', '=', $specific['status']);}
                    $query = $query->orderBy('status', 'DESC');
                }
                else{
                    $query = Consultation::where('consultant_id', '=', $specific)->orWhere('consultant_seen_id', '=', $specific);
                }
            break;
            case 'mine':
                $query = Consultation::where('consultant_id', '=', $this->authId());
                if( isset($specific['start_date'])){ $query->where('date', '>=', $specific['start_date']);}
                if(isset($specific['end_date'])){$query->where('date', '<=', $specific['end_date']);}
                if(isset($specific['status'])){$query->where('status', '=', $specific['status']);}
                $query = $query->orderBy('status', 'DESC');    
            break;
            case 'patient':
                if (is_array($specific)) {
                    $query = Consultation::where('patient_id', '=', $specific)->orderBy('status', 'DESC');
                    if( isset($specific['start_date'])){ $query->where('date', '>=', $specific['start_date']);}
                    if(isset($specific['end_date'])){$query->where('date', '<=', $specific['end_date']);}
                }
                else{
                    $query = Consultation::where('patient_id', '=', $specific);
                }
            break;
            case 'specialty':
                $specialties = $specific ?? SpecialtyDoctor::where('doctor_id', '=', Auth::id()?? auth('api')->id())->pluck('specialty_id');
                $query = Consultation::whereIn('specialty_id', $specialties)->orderBy('status', 'DESC');
                if( isset($specific['start_date'])){ $query->where('date', '>=', $specific['start_date']);}
                if(isset($specific['end_date'])){$query->where('date', '<=', $specific['end_date']);}
            break;
            case 'status':
                $query = Consultation::where('status', '=', $specific);
                if( isset($specific['start_date'])){ $query->where('date', '>=', $specific['start_date']);}
                if(isset($specific['end_date'])){$query->where('date', '<=', $specific['end_date']);}
            break;
        }

        $query = $detailed ? $query->with(['consultant_seen', 'consultant', 'patient.user', 'visit']) : $query->select('id', 'unique_id', 'patient_id')->with(['patient.user']);
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_consultation_get_by($type, $specific, $detailed){
        switch($type){
            case 'id':
                $query = Consultation::where('id', '=', $specific);
            break;
            case 'unique_id':
                $query = Consultation::where('unique_id', '=', $specific);
            break;
        }

        $query = $detailed ? $query->with(['consultation_type', 'patient.user', 'specialty', 'transaction', 'laboratory.item', 'radiology.test', 'prescriptions.drugs'])->first() : $query->select('id', 'unique_id', 'patient_id')->with('patient.user')->first();

        return $query;
    }

    public function emr_consultation_start_request($id){
        DB::beginTransaction();

        try{
            $query = Consultation::findOrFail($id);
            
            $query->consultant_seen_id = $this->authId();
            $query->start_time = date('Y-m-d H:i:s');
            $query->status = 3;
            $query->updated_by = $this->authId();

            $query->save();

            $this->log_user_activity('Consultation start', $id, true);
            DB::commit();
            return $query;
        }
    
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Consultation start', $id, false);
            return $e->getMessage();
        }
    }
}