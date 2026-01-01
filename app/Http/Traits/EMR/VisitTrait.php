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
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Appointment\Appointment;

use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\SpecialtyDoctor;
use App\Models\EMR\Consultation\Service as ConsultationService;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance;
use App\Models\EMR\Visit;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait VisitTrait{
    use FileManagerTrait, LogTrait, TransactionTrait;

    private function emr_visit_unique_id_create(){
        return config('app.short_code').'-'.dechex(time());
    }

    public function emr_appointment_cancel($id){
        DB::beginTransaction();

        try{
            $appointment = Appointment::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstorFail();
            if($appointment->visit_id){
                $this->log_user_activity('Appointment Conversion to Visit', $id, false);
                return 'This appointment has already been converted to a visit.';
            }
            $appointment->status = Appointment::StatusCancelled;
            $appointment->save();
        }
        catch(Exception $e){}
    }

    public function emr_appointment_convert_to_visit($data, $id)
    {
        DB::beginTransaction();

        try {

            /** ------------------------------
             * 1. Load appointment
             * ------------------------------ */
            $appointment = Appointment::where('id', '=', $id)->orWhere('unique_id','=', $id)->firstOrFail();

            if ($appointment->visit_id) {throw new Exception('This appointment already has a visit.');}

            /** ------------------------------
             * 2. Load patient
             * ------------------------------ */
            $patient = Patient::findOrFail($appointment->patient_id);

            /** ------------------------------
             * 3. Ensure no active visit
             * ------------------------------ */
            $activeVisitExists = Visit::where('patient_id', '=', $patient->id)->whereIn('status', [Visit::StatusStarted, Visit::StatusAdmitted])->exists();

            if ($activeVisitExists) {throw new Exception('Patient already has an active visit.');}

            /** ------------------------------
             * 4. Handle unregistered patient
             * ------------------------------ */
            $wasUnregistered = false;

            if ($patient->type == 2) {
                $patient->type = 1; // Registered
                $patient->save();

                $wasUnregistered = true;
            }

            /** ------------------------------
             * 5. Create Visit
             * ------------------------------ */
            $visit = Visit::create([
                'unique_id'        => $this->emr_visit_unique_id_create(),
                'branch_id'        => $appointment->branch_id,
                'patient_id'       => $patient->id,
                'plan_id'          => $data['plan_id'] ?? $patient->primary_care_id,
                'start_date'       => $data['start_date'] ?? now()->toDateString(),
                'start_timestamp'  => $data['start_timestamp'] ?? now(),
                'status'           => Visit::StatusStarted,
                'created_by'       => Auth::id() ?? auth('api')->id(),
                'updated_by'       => Auth::id() ?? auth('api')->id(),
            ]);

            /** ------------------------------
             * 6. Attach visit to appointment
             * ------------------------------ */
            $appointment->update([
                'visit_id'       => $visit->id,
            ]);

            /** ------------------------------
             * 7. Registration billing (if new)
             * ------------------------------ */
            if ($wasUnregistered) {
                $registrationServiceId = config('emr.registration_service_id');

                $this->finance_transaction_create($registrationServiceId, $patient->id, 1, true, $visit->id);
            }

            /** ------------------------------
             * 8. Consultation billing
             * ------------------------------ */
            if ($appointment->service_type_id == 4) { // Consultation
                    $itemId = ConsultationService::resolveItemId($appointment->specialty_id,$appointment->consultant_id);

                    if ($itemId) {$this->finance_transaction_create($itemId, $patient->id, 1, true, $visit->id);}
                }

            $this->log_user_activity('Appointment Conversion to Visit', $appointment->id,true);
            DB::commit();
            return $visit;

        } catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Appointment Conversion to Visit', $id, false);

            return $e->getMessage();
        }
    }

    public function emr_appointment_create($data){

        DB::beginTransaction();

        try{
            $query = Appointment::create([
                'unique_id' => $this->emr_visit_unique_id_create(),
                'branch_id' => $data['branch_id'] ?? request()->cookie('current_branch'),
                'patient_id' => $data['patient_id'] ?? request()->cookie('current_patient'),
                'plan_id' => $data['plan_id'] ?? null,
                'consultant_id' => $data['consultant_id'] ?? null,
                'specialty_id' => $data['specialty_id'] ?? null,
                'service_type_id' => $data['service_type_id'],
                'date' => $data['date'],
                'time_slot' => $data['time_slot'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'type' => $dat['type'] ?? Appointment::TypeStaff,
                'status' => $data['status'] ?? Appointment::StatusPending,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Appointment Creation', $query->id, true);
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Appointment Creation', null, false);
            return $e->getMessage();
        }
    }

    public function emr_appointment_get_all($type, $specific, $detailed, $paginated, $page){
        //echo request()->cookie('current_branch');
        $query = Appointment::where('branch_id', '=', request()->cookie('current_branch'));
        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'cancelled':
                $query = $query->where('status', '=', Appointment::StatusCancelled);
            break;
            case 'checked_in':
                $query = $query->where('status', '=', Appointment::StatusCheckedIn);
            break;
            case 'completed':
                $query = $query->where('status', '=', Appointment::StatusCompleted);
            break;
            case 'confirmed':
                $query->where('status', '=', Appointment::StatusConfirmed);
            break;
            case 'no_show':
                $query->where('status', '=', Appointment::StatusNoShow);
            break;
            case 'pending':
                $query->where('status', '=', Appointment::StatusPending);
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['date'])){
                $query->whereDate('date', '=', $specific['date']);
            }

            if(!empty($specific['service_type_id'])){
                $query->where('service_type_id', '=', $specific['service_type_id']);
            }

            if(!empty($specific['specialty_id'])){
                $query->where('specialty_id', '=', $specific['specialty_id']);
            }
        }

        $query = $detailed ? $query->with(['branch', 'consultant', 'patient.user', 'patient.insurances', 'specialty', 'service_type']) : $query->select('id', 'unique_id', 'patient_id')->with(['patient.user']);
        $query = $paginated ? $query->latest()->paginate(50) : $query->latest()->get();

        return $query;
    }

    public function emr_appointment_get_by($type, $id, $detailed){
        try{
            $query = Appointment::where('unique_id', '=', $id)->orWhere('id', '=', $id);
            $query = $detailed ? $query->with(['branch', 'patient.user', 'whom_to_see', 'specialty', 'service']) : $query->with(['patient.user']);
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_appointment_search($data){
        $query = Appointment::with(['patient', 'doctor']);

        if ($data->filled('doctor_id')) {$query->where('doctor_id', $data->doctor_id);}

        if ($data->filled('consultation_group_id')) {$query->where('consultation_group_id', $data->consultation_group_id);}

        if ($data->filled('patient_name')) {
            $query->whereHas('patient', function ($q) use ($data) {
                $q->where('first_name', 'like', '%'.$data->patient_name.'%')
                ->orWhere('other_name', 'like', '%'.$data->patient_name.'%')  
                ->orWhere('last_name', 'like', '%'.$data->patient_name.'%');
            });
        }

        if ($data->filled('date')) {$query->whereDate('appointment_at', $data->date);}
    }

    public function emr_appointment_update($data, $id){
        DB::beginTransaction();

        try{
            $appointment = Appointment::where('id', '=', $id)->first();

            $appointment->branch_id = $data['branch_id'] ?? request()->cookie('current_branch');
            $appointment->patient_id = $data['patient_id'] ?? request()->cookie('current_patient');
            $appointment->care_id = $data['care_id'];
            $appointment->whom_to_see_id = $data['whom_to_see_id'];
            $appointment->specialty_id = $data['specialty_id'];
            $appointment->service_id = $data['service_id'];
            $appointment->timestamp = $data['timestamp'] ?? null;
            $appointment->visit_type_id = $data['visit_type_id'];
            $appointment->updated_by = Auth::id() ?? auth('api')->id();

            $appointment->save();
            DB::commit();
            $this->log_user_activity('Appointment Update', $id, true);
            return $appointment;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Appointment Update', null, false);
            return $e->getMessage();
        }
    }

    public function emr_visit_active_patients($branch_id){
        $patients = Visit::where('branch_id', '=', $branch_id)->whereIn('status', [1])->select('patient_id', 'unique_id')->with(['patient.user'])->get();

        return $patients;
    }
    
    public function emr_visit_cancel($id){
        DB::beginTransaction();

        try{
            $visit = Visit::where('id', '=', $id)->first();

            $transactions = $this->finance_transaction_get_all('visit', $id, false, false, null);
            foreach ($transactions as $transaction){
                $this->finance_transaction_cancel($transaction->id);
            }

            $visit->end_date = date('Y-m-d');
            $visit->end_timestamp = date('Y-m-d H:i:s');
            $visit->status = 2;
            $visit->updated_by = auth('api')->id();

            $visit->save();
            DB::commit();
            $this->log_user_activity('Visit Request Cancellation', $id, true);
            return $visit;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Visit Request Cancellation', $id, false);
            return $e->getMessage();
        }
        
    }

    public function emr_visit_create($data){
        DB::beginTransaction();

        try{
            $query = Visit::create([
                'unique_id' => $this->emr_visit_unique_id_create(),
                'branch_id' => $data['branch_id'] ?? request()->cookie('current_branch'),
                'patient_id' => $data['patient_id'] ?? request()->cookie('current_patient'),
                'care_id' => $data['care_id'],
                'start_date' => $data['start_date'] ?? date('Y-m-d'),
                'start_timestamp' => $data['start_timestamp'] ?? null,
                'end_date' => $data['end_date'] ?? null,
                'end_timestamp' => $data['end_timestamp'] ?? null,
                'visit_type_id' => $data['visit_type_id'],
                'status' => $data['status'] ?? 0,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
  
            DB::commit();
            $this->log_user_activity('Visit Request Creation', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Visit Request Creation', null, false);
            return $e->getMessage();
        }
    }

    public function emr_visit_create_from($data){}

    public function emr_visit_end($id){
        DB::beginTransaction();

        try{
            $visit = Visit::where('id', '=', $id)->first();

            $transactions = $this->finance_transaction_get_all('visit_pending', $id, false, false, null);
            foreach ($transactions as $transaction){
                $this->finance_transaction_cancel($transaction->id);
            }

            $visit->end_date = date('Y-m-d');
            $visit->end_timestamp = date('Y-m-d H:i:s');
            $visit->status = 2;
            $visit->updated_by = auth('api')->id();

            $visit->save();
            DB::commit();
            $this->log_user_activity('Visit Request Cancellation', $id, true);
            return $visit;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Visit Request Cancellation', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_visit_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Visit::where('branch_id', '=', request()->cookie('current_branch'));
        
        switch ($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'branch_active':
                $query = $query->where('status', '=', 1);
            break;
            case 'finished':
                $query = $query->where('status', '=', 2);
            break;
        }

        $query = $detailed ? $query->with(['patient.user', 'branch']) : $query->select('id', 'unique_id', 'patient_id')->with(['patient.user']);
        $query = $paginated ? $query->latest()->paginate(50) : $query->latest()->get();

        return $query;
    }

    public function emr_visit_get_by($type, $id, $detailed){
        switch($type){
            case 'id':
                $query = Visit::where('id', '=', $id);
            break;
            case 'unique_id':
                $query = Visit::where('unique_id', '=', $id);
            break;
        }

        $query = $detailed ? $query->with(['branch', 'patient.user', 'price_list', 'visit_type']) :$query->with(['patient.user']);

        return $query->first();
    }

    public function emr_visit_patient_pending($patient_id){
        $query = Visit::where('patient_id', '=', $patient_id)->where('status', '=', 1)->get();

        if ($query->count() > 0){
            return $query;
        }
        else{
            return null;
        }
    }

    public function emr_visit_start($data){}
    
    public function emr_visit_update($data){}
}