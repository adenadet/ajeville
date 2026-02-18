<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\AppointmentTrait;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\NursingTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Http\Traits\EMR\PhysiotheraphyTrait;
use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Http\Traits\EMR\RadiologyTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\Specialty;
use App\Models\EMR\Consultation\SpecialtyDoctor;

use App\Models\Operations\Branch;
use App\Models\EMR\Dialysis\Request as DialysisRequest;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance;
use App\Models\EMR\Visit;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait DialysisTrait{

    use LogTrait, VisitTransactionTrait;

    public function emr_dialysis_request_cancel($id){
        DB::beginTransaction();
        try{
            $query = DialysisRequest::where('id', '=', $id)->orWhere('unique_id', '=', $id)->firstOrFail();

            if ($query->status > DialysisRequest::StatusPending){
                throw new Exception('Request has already been initiated');
            }

            if (is_null($query->deleted_at)){
                $query->deleted_at = date('Y-m-d H:i:s');
                $query->deleted_by = auth('api')->id() ?? Auth::id();
            }
            else{
                $query->deleted_at = null;
                $query->deleted_by = null;
            }

            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            $this->log_user_activity('EMR Dialysis Request Cancel', $query->id, true);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Dialysis Request Cancel', null, false);
            return $e->getMessage();
        } 
    }
    public function emr_dialysis_request_create($data){
        DB::beginTransaction();
        try{
            $transaction = $this->emr_visit_transaction_create($data['item_id'], $data['patient_id'], 1, true, $data['visit_id']);
            $visit = Visit::findOrFail($data['visit_id']);
            $query = DialysisRequest::create([
                'date' => $data['date'] ?? date('Y-m-d'),
                'visit_id' => $visit->id ?? null,
                'branch_id' => $data['branch_id'] ?? $visit->branch_id ?? $consultation->branch_id ?? request()->cookie('current_branch'),
                'consultation_id' => $data['consultation_id'] ?? null,
                'patient_id' => $data['patient_id'],
                'transaction_id' => $transaction->id,
                'quantity' => 1,
                'item_id' => $data['item_id'],
                'status' => DialysisRequest::StatusPending,
            ]);

            $this->log_user_activity('EMR Dialysis Request Create', $query->id, true);
            DB::commit();

            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Dialysis Request Create', null, false);
            return $e->getMessage();
        }
    }

}