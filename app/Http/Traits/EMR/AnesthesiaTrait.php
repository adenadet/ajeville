<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\ProcedureTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Anesthesia\DrugAdmin;
use App\Models\EMR\Anesthesia\InOp;
use App\Models\EMR\Anesthesia\PostOp;
use App\Models\EMR\Anesthesia\PreOp;
use App\Models\EMR\Anesthesia\RequestCase;
use App\Models\EMR\Anesthesia\VitalSign;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\SpecialtyDoctor;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance;
use App\Models\EMR\Visit;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait AnesthesiaTrait{
    use LogTrait;
    public function emr_anesthesia_case_create($data){
        DB::beginTransaction();

        try{    
            $query = RequestCase::create([
                'visit_id' => $data['visit_id'],
                'procedure_id' => $data['procedure_id'],
                'date' => $data['date'] ?? date('Y-m-d'),
                'patient_id' => $data['patient_id'],
                'anesthesia_type' => $data['anesthesia_type'] ?? RequestCase::AnesthesiaTypeLocal,
                'asa_class' => $data['asa_class'] ?? 6,
                'assigned_anesthetist_id' => $data['assigned_anesthetist_id'] ?? null,
                'remarks' => $data['remarks'],
                'status' => $data['status'] ?? RequestCase::StatusRequested,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by'=> auth('api')->id() ?? Auth::id(),
            ]);
            DB::commit();
            $this->log_user_activity('EMR Anesthesist Request Created', null, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Request Created', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_case_deactivate($id){
        DB::beginTransaction();

        try{    
            $query = RequestCase::where('id', '=', $id)->firstOrFail();

            if (is_null($query->deleted_at)){
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();
            DB::commit();
            $this->log_user_activity('EMR Anesthesist Request Deactivated', null, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Request Deactivated', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_case_get_all($type, $specific, $detailed, $paginated){
        $query = RequestCase::query();

        switch($type){
            case 'all':
                $query->withTrashed();
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = $specific['query'];
                $users = User::where('first_name', 'LIKE', "%$search%")->orWhere('last_name', 'LIKE', "%$search%")->orWhere('middle_name', 'LIKE', "%$search%")->pluck('id');
                $patients = Patient::whereIn('user_id', $users)->pluck('id')->orWhere('unique_id', 'LIKE', "%$search%");
                $query = $query->orWhereIn('patient_id', $patients)->orWhereIn('assigned_anesthetist_id', $users);
            }

            if(!empty($specific['start_date'])){
                $query = $query->whereDate('start_date', '>=', $specific['start_date']);
            }

            if(!empty($specific['end_date'])){
                $query = $query->whereDate('end_date', '<=', $specific['end_date']);
            }
        }

        $query = $detailed ? $query->with(['anesthesist', 'patient', 'procedure',]) : $query;
        $query->orderBy('date', 'DESC');
        $query = $paginated ? $query->paginate(40) : $query->get();

        return $query; 
    }    
    
    public function emr_anesthesia_case_get_by($type, $id, $detailed){
        try{
            $query = RequestCase::where('id', '=', $id);
            $query = $detailed ? $query->with(['anesthesist', 'patient', 'procedure',]) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_anesthesia_case_update($data, $id){
        DB::beginTransaction();

        try{    
            $query = RequestCase::where('id', '=', $id)->firstOrFail();
            
            $query->visit_id = $data['visit_id'] ?? $query->visit_id;
            $query->procedure_id = $data['procedure_id'] ?? $query->procedure_id;
            $query->date = $data['date']  ?? $query->date;
            $query->patient_id = $data['patient_id'] ?? $query->patient_id;
            $query->anesthesia_type = $data['anesthesia_type'] ?? $query->anesthesia_type;
            $query->asa_class = $data['asa_class'] ?? $query->asa_class;
            $query->assigned_anesthetist_id = $data['assigned_anesthetist_id'] ?? $query->assigned_anesthetist_id;
            $query->remarks = $data['remarks'] ?? $query->remarks;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Request Updated', null, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Request Updated', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_drug_admin_create($data){
        DB::beginTransaction();

        try{    
            $query = DrugAdmin::create([
                'case_id' => $data['case_id'],
                'drug_id' => $data['drug_id'],
                'route_id' => $data['route_id'],
                'dose' => $data['dose'],
                'quantity' => $data['quantity'],
                'time' => $data['time'],
                'remarks' => $data['remarks'] ?? 'As prescribed by Anesthesist',
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Drug Administration Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Drug Administration Created', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_drug_admin_deactivate($id){
         DB::beginTransaction();

        try{    
            $query = DrugAdmin::where('id', '=', $id)->firstOrFail();
            
            if (is_null($query->deleted_at)){
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_by = null;
                $query->deleted_at = null;
            }
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            
            DB::commit();
            $this->log_user_activity('EMR Anesthesist Drug Administration Deactivated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Drug Administration Deactivated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_drug_admin_get_all($type, $specific, $detailed, $paginated){
        $query = RequestCase::query();

        switch($type){
            case 'all':
                $query->withTrashed();
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['case_id'])){
                $query = $query->where('case_id', '=', $specific['case_id']);
            }
        }

        $query = $detailed ? $query->with(['case.procedure', 'drug', 'route', 'creator', 'updater']) : $query;
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_anesthesia_drug_admin_get_by($id, $detailed){
        try{
            $query = DrugAdmin::where('id', '=', $id);
            $query = $detailed ? $query->with(['case.procedure', 'drug', 'route', 'creator', 'updater']) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_anesthesia_drug_admin_update($data, $id){
        DB::beginTransaction();
        try{    
            $query = DrugAdmin::where('id', '=', $id)->firstOrFail();
            
            $query->case_id = $data['case_id'] ?? $query->case_id;
            $query->drug_id = $data['drug_id'] ?? $query->drug_id;
            $query->route_id = $data['route_id'] ?? $query->route_id;
            $query->dose = $data['dose'] ?? $query->dose;
            $query->quantity = $data['quantity'] ?? $query->quantity;
            $query->time = $data['time'] ?? $query->time;
            $query->remarks = $data['remarks']  ?? $query->remarks;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();
            
            DB::commit();
            $this->log_user_activity('EMR Anesthesist Drug Administration Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Drug Administration Updated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_in_operation_create($data){
        DB::beginTransaction();

        try{
            $query = InOp::create([
                'case_id' => $data['case_id'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'] ?? null,
                'airway_device' => $data['airway_device'],
                'ventilation_mode' => $data['ventilation_mode'],
                'remarks' => $data['remarks'],
                'status' => $data['status'] ?? InOp::StatusInProgress,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            foreach($data['drug_admins'] as $drug_admin){
                DrugAdmin::create([
                    'case_id' => $query->id,
                    'drug_id' => $drug_admin['drug_id'],
                    'route_id' => $drug_admin['route_id'],
                    'dose' => $drug_admin['dose'],
                    'quantity' => $drug_admin['quantity'],
                    'time' => $drug_admin['time'],
                    'remarks' => $drug_admin['remarks'] ?? 'As prescribed by Anesthesist',
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }

            foreach($data['vital_signs'] as $vital_sign){
                $query = VitalSign::create([
                    'case_id' => $query->id,
                    'time' => $vital_sign['time'] ?? date('Y-m-d H:i:s'),
                    'blood_pressure' => $vital_sign['blood_pressure'] ?? null,
                    'pulse' => $vital_sign['pulse'] ?? null,
                    'spo2' => $vital_sign['spo2'] ?? null,
                    'ecg' => $vital_sign['ecg'] ?? null,
                    'etco2' => $vital_sign['etco2'] ?? null,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Created', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_in_operation_deactivate($id){
        DB::beginTransaction();

        try{
            
            $query = PreOp::findOrFail($id);

            if(is_null($query->deleted_at)){
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Deactivated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Deactivated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_in_operation_get_all($type, $specific, $detailed, $paginated){
        $query = PreOp::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break; 
        }

        if (is_array($specific)){
            if(!empty($specific['case_id'])){
                $query = $query->where('case_id', '=', $specific['case_id']);
            }
        }

        $query = $detailed ? $query->with(['case.procedure', 'creator', 'deleter', 'updater']) : $query;
        $query = $paginated ? $query->paginate(20) : $query->get();
    }

    public function emr_anesthesia_in_operation_get_by($type, $id, $detailed){
        try{
            $query = PreOp::where('id', '=', $id);
            $query = $detailed ? $query->with(['case.procedure', 'creator', 'deleter', 'updater']) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_anesthesia_in_operation_update($data, $id){
        DB::beginTransaction();

        try{
            $query = InOp::findOrFail($id);

            $query->case_id = $data['case_id'] ?? $query->case_id;
            $query->accessed_by = $data['accessed_by'] ?? $query->accessed_by;
            $query->date = $data['date'] ?? $query->date;
            $query->airway_score = $data['airway_score'] ?? $query->airway_score;
            $query->risk_notes = $data['risk_notes'] ?? $query->risk_notes;
            $query->fitness = $data['fitness'] ?? $query->fitness;
            $query->recommendations = $data['recommendations'] ?? $query->recommendations;
            $query->anesthesia_type = $data['anesthesia_type'] ?? $query->anesthesia_type;
            $query->consent_id = $consent->id ?? null;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Updated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_post_operation_create($data){
        DB::beginTransaction();

        try{
            $query = PostOp::create([
                'case_id' => $data['case_id'],
                'monitored_by' => $data['monitored_by'] ?? Auth::id() ?? auth('api')->id(),
                'start_time' => $data['start_time'] ?? date('Y-m-d H:i:s'),
                'end_time' => $data['end_time'] ?? null,
                'aldrete_score' => $data['aldrete_score'] ?? 0,
                'pain_score' => $data['pain_score'] ?? 0,
                'complications' =>  $data['complications'] ?? null,
                'vital_stable' => $data['vital_stable'] ?? 1,
                'airway_patency' => $data['airway_patency'] ?? 1,
                'nausea' => $data['nausea'] ?? 0,
                'clearance_status' => $data['clearance_status'] ?? PostOp::ClearanceStatusCleared,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Created', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_post_operation_deactivate($id){
        DB::beginTransaction();

        try{
            
            $query = PostOp::findOrFail($id);

            if(is_null($query->deleted_at)){
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Deactivated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Deactivated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_post_operation_get_all($type, $specific, $detailed, $paginated){
        $query = PostOp::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break; 
        }

        if (is_array($specific)){
            if(!empty($specific['case_id'])){
                $query = $query->where('case_id', '=', $specific['case_id']);
            }
        }

        $query = $detailed ? $query->with(['case.procedure', 'creator', 'deleter', 'updater']) : $query;
        $query = $paginated ? $query->paginate(20) : $query->get();
    }

    public function emr_anesthesia_post_operation_get_by($type, $id, $detailed){
        try{
            $query = PostOp::where('id', '=', $id);
            $query = $detailed ? $query->with(['case.procedure', 'creator', 'deleter', 'updater']) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_anesthesia_post_operation_update($data, $id){
        DB::beginTransaction();

        try{
            $query = PostOp::findOrFail($id);

            $query->case_id = $data['case_id'] ?? $query->case_id;
            $query->monitored_by = $data['monitored_by'] ?? $query->monitored_by;
            $query->start_time = $data['start_time'] ?? $query->start_time;
            $query->end_time = $data['end_time'] ?? $query->start_time;
            $query->aldrete_score = $data['aldrete_score'] ?? $query->aldrete_score;
            $query->pain_score = $data['pain_score'] ?? $query->pain_score;
            $query->complications =  $data['complications'] ?? $query->complications;
            $query->vital_stable = $data['vital_stable'] ?? $query->vital_stable;
            $query->airway_patency = $data['airway_patency'] ?? $query->airway_patency;
            $query->nausea = $data['nausea'] ?? $query->nausea;
            $query->clearance_status = $data['clearance_status'] ?? $query->clearance_status;
            $query->remarks = $data['remarks'] ?? $query->remarks;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Updated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_pre_operation_create($data){
        DB::beginTransaction();

        try{
            $consent = null;
            $query = PreOp::create([
                'case_id' => $data['case_id'],
                'accessed_by' => $data['accessed_by'] ?? auth('api')->id() ?? Auth::id(),
                'date' => $data['date'] ?? date('Y-m-d'),
                'airway_score' => $data['airway_score'],
                'risk_notes' => $data['risk_notes'] ?? null,
                'fitness' => $data['fitness'] ?? true,
                'recommendations' => $data['recommendations'],
                'anesthesia_type' => $data['anesthesia_type'],
                'consent_obtained' => $consent ? true : false,
                'consent_id' => $consent->id ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Created', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_pre_operation_deactivate($id){
        DB::beginTransaction();

        try{
            
            $query = PreOp::findOrFail($id);

            if(is_null($query->deleted_at)){
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_by = null;
                $query->deleted_at = null;
            }

            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Deactivated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Deactivated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_pre_operation_get_all($type, $specific, $detailed, $paginated){
        $query = PreOp::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break; 
        }

        if (is_array($specific)){
            if(!empty($specific['case_id'])){
                $query = $query->where('case_id', '=', $specific['case_id']);
            }
        }

        $query = $detailed ? $query->with(['case.procedure', 'creator', 'deleter', 'updater']) : $query;
        $query = $paginated ? $query->paginate(20) : $query->get();
    }

    public function emr_anesthesia_pre_operation_get_by($type, $id, $detailed){
        try{
            $query = PreOp::where('id', '=', $id);
            $query = $detailed ? $query->with(['case.procedure', 'creator', 'deleter', 'updater']) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_anesthesia_pre_operation_update($data, $id){
        DB::beginTransaction();

        try{
            $consent = null;
            $query = PreOp::findOrFail($id);

            $query->case_id = $data['case_id'] ?? $query->case_id;
            $query->accessed_by = $data['accessed_by'] ?? $query->accessed_by;
            $query->date = $data['date'] ?? $query->date;
            $query->airway_score = $data['airway_score'] ?? $query->airway_score;
            $query->risk_notes = $data['risk_notes'] ?? $query->risk_notes;
            $query->fitness = $data['fitness'] ?? $query->fitness;
            $query->recommendations = $data['recommendations'] ?? $query->recommendations;
            $query->anesthesia_type = $data['anesthesia_type'] ?? $query->anesthesia_type;
            $query->consent_obtained = $consent ? true : false;
            $query->consent_id = $consent->id ?? null;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Pre Operation Assessment Updated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_vital_sign_create($data){
        DB::beginTransaction();

        try{
            $query = VitalSign::create([
                'case_id' => $data['case_id'],
                'time' => $data['time'] ?? date('Y-m-d H:i:s'),
                'blood_pressure' => $data['blood_pressure'] ?? null,
                'pulse' => $data['pulse'] ?? null,
                'spo2' => $data['spo2'] ?? null,
                'ecg' => $data['ecg'] ?? null,
                'etco2' => $data['etco2'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);    
            DB::commit();
            $this->log_user_activity('EMR Anesthesist Vital Sign Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Vital Sign Created', null, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_vital_sign_deactivate($data, $id){
        DB::beginTransaction();

        try{
            $query = VitalSign::where('id', '=', $id)->firstOrFail();
            
            if(is_null($query->deleted_at)){
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }
            else{
                $query->deleted_by = null;
                $query->deleted_at = null;
            }
            
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Vital Sign Deactivated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Vital Sign Deactivated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }

    public function emr_anesthesia_vital_sign_get_all($type, $specific, $detailed, $paginated){
        $query = VitalSign::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
        }

        if (is_array($specific)){
            if(!empty($specific['case_id'])){
                $query = $query->where('case_id', '=', $specific['case_id']);
            }
        }

        $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query;
        $query = $query->orderBy('time', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_anesthesia_vital_sign_get_by($type, $id, $detailed){
        try{
            $query = VitalSign::where('id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_anesthesia_vital_sign_update($data, $id){
        DB::beginTransaction();

        try{
            $query = VitalSign::where('id', '=', $id)->firstOrFail();
                
            $query->case_id = $data['case_id'] ?? $query->case_id;
            $query->time = $data['time'] ?? $query->time;
            $query->blood_pressure = $data['blood_pressure'] ?? $query->blood_pressure;
            $query->pulse = $data['pulse'] ?? $query->pulse;
            $query->spo2 = $data['spo2'] ?? $query->spo2;
            $query->ecg = $data['ecg'] ?? $query->ecg;
            $query->etco2 = $data['etco2'] ?? $query->etco2;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('EMR Anesthesist Vital Sign Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Anesthesist Vital Sign Updated', $id, false);
            return 'Error: '.$e->getMessage();
        }
    }
}