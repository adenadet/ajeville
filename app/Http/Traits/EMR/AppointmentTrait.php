<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\DialysisTrait;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\NursingTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Http\Traits\EMR\PhysiotheraphyTrait;
use App\Http\Traits\EMR\RadiologyTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Appointment\Appointment;
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

trait AppointmentTrait{
    use LogTrait;

    private function emr_appointment_unique_id($type){

    }

    public function emr_appointment_create($data){
        DB::beginTransaction();

        try{
            // 1) Create Appointment
            $appointment = Appointment::create([
                'branch_id' => $data['branch_id'],
                'patient_id' => $data['patient_id'],
                'care_id' => $data['care_id'] ?? null,
                'date' => $data['date'],
                'time' => $data['time'],
                'whom_to_see_id' => $data['doctor_id'] ?? null,
                'service_id' => $data['service_id'] ?? null,
                'specialty_id' => $data['specialty_id'] ?? null,
                'status' => $data['status'] ?? 0,
                'unique_id' => $this->emr_appointment_unique_id('appointment'),
                'visit_type_id' => $data['visit_type_id'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
        
            if (isset($appointment->care_id)){

            }
            DB::commit();
            $this->log_user_activity('EMR Appointment Created', null, true);
            return $appointment;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Appointment Created', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_appointment_update($data, $id){
        return 'success';
    }
}