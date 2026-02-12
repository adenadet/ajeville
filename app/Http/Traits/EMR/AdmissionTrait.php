<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\ProcedureTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\EMR\Admission\Bed;
use App\Models\EMR\Admission\BedAssignment;
use App\Models\EMR\Admission\BedAssignmentAudit;
use App\Models\EMR\Admission\Category;
use App\Models\EMR\Admission\PreAdmissionCheck;
use App\Models\EMR\Admission\Reason;
use App\Models\EMR\Admission\Request as AdmissionRequest;
use App\Models\EMR\Admission\Room;
use App\Models\EMR\Admission\RoomType;
use App\Models\EMR\Admission\Service as AdmissionService;
use App\Models\EMR\Admission\Type;
use App\Models\EMR\Admission\Ward;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\SpecialtyDoctor;
use App\Models\Operations\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance;
use App\Models\EMR\Service as EMRService;
use App\Models\EMR\Visit;
use App\Models\EMR\VisitTransaction;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use App\Models\User;
use App\Services\EMR\BedBillingService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait AdmissionTrait{
    use ItemTrait, LogTrait, VisitTransactionTrait;

    /*
    --------------------------------------------------------------
    Admission Bed Functions
    --------------------------------------------------------------
    */

    public function admission_bed_create($data){
        DB::beginTransaction();
        try{
            $room = Room::findOrFail($data['room_id']);
            $query = Bed::create([
                'name' => $data['name'],
                'bed_code' => $data['bed_code'] ?? '',
                'ward_id' => $room->ward_id, 
                'room_type_id' => $room->room_type_id, 
                'room_id' => $room->id,
                'assignment_status' => Bed::AssignmentStatusFree,
                'status' => $data['status'] ?? Bed::StatusActive,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id()
            ]);

            DB::commit();
            $this->log_user_activity('Admission Bed Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Bed Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_bed_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Bed::find($id);
            if ($query->status == Bed::StatusInActive) {
                $query->status = Bed::StatusActive;
                $query->deleted_at = null;
                $query->deleted_by = null;
            } 
            else {
                $query->status = Bed::StatusInActive;
                $query->deleted_at = now();
                $query->deleted_by = auth('api')->id() ?? Auth::id();
            }
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Bed Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Bed Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_bed_get_all($type, $specific, $detailed, $paginated){
        $query = Bed::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', Bed::StatusActive);
                break;
            case 'all':
                $query = $query->withTrashed();
                break;
            case 'inactive':
                $query = $query->where('status', '=', Bed::StatusInActive)->withTrashed();
                break;
        }

        if (is_array($specific)) {
            if (!empty($specific['ward_id'])) {
                $query = $query->where('ward_id', '=', $specific['ward_id']);
            }
            if (!empty($specific['room_type_id'])) {
                $query = $query->where('room_type_id', '=', $specific['room_type_id']);
            }
            if(!empty($specific['room_id'])){
                $query = $query->where('room_id', '=', $specific['room_id']);
            }
        }

        $query = $detailed ? $query->with(['ward', 'room', 'room_type']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function admission_bed_get_by($type, $id, $detailed){
        try{
            $query = Bed::where('id', '=', $id);
            $query = $detailed ? $query->with(['ward', 'room', 'room_type']) : $query->select('id', 'name');
            $query = $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function admission_bed_release($data, $id){
        DB::beginTransaction();
        try{
            $query = Bed::find($id);
            $billing_service = new BedBillingService();
            $assignments = BedAssignment::where('bed_id', '=', $id)->whereIn('assignment_status', [ BedAssignment::StatusPending, BedAssignment::StatusAssigned])->get();

            foreach($assignments as $assignment){
                if ($assignment->status == BedAssignment::StatusPending){
                    //If it is still pending then delete the payment 
                    $billing_service->deleteBedCharge($assignment);
                }
                else if($assignment->status == BedAssignment::StatusAssigned){
                    $billing_service->reverseBedCharge($assignment, false);
                }

                $assignment->released_by = auth('api')->id() ?? Auth::id();
                $assignment->released_at = now();
                $assignment->status = BedAssignment::StatusReleased;
                $assignment->updated_by = auth('api')->id() ?? Auth::id();
                $assignment->save();
            }
            
            $query->update([
                'assignment_status' => Bed::AssignmentStatusFree,
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('Admission Bed Release', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Bed Release', $id, false);
            return $e->getMessage();
        }
    }

    public function admission_bed_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Bed::find($id);
            
            $query->name = $data['name'] ?? $query->name;
            $query->ward_id = $data['ward_id'] ?? $query->ward_id; 
            $query->room_type_id = $data['room_type_id'] ?? $query->room_type_id; 
            $query->bed_code = $data['bed_code'] ?? $query->bed_code;
            $query->room_id = $data['room_id'] ?? $query->room_id;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Bed Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Bed Created', null, false);
            return $e->getMessage();
        }
    }

    
    /*
    --------------------------------------------------------------
    Admission Bed Functions
    --------------------------------------------------------------
    */
    public function admission_bed_assignment_create($data){
        DB::beginTransaction();

        try{
            $billing_service = new BedBillingService();
            $bed = Bed::lockForUpdate()->findOrFail($data['bed_id']);

            if ($bed->assignment_status !== Bed::AssignmentStatusFree) {
                throw new Exception('Bed is not available');
            }

            $admission = AdmissionRequest::with('visit')->where('id', '=', $data['admission_id'])->firstOrFail();
            $room_type_id = $bed->room_type_id;
            $room_type = RoomType::find($room_type_id);

            if ($data['patient_id'] != $admission->patient_id) {
                throw new Exception('Patient does not match admission');
            }
            
            $query = BedAssignment::create([
                'bed_id' => $data['bed_id'],
                'admission_id' => $data['admission_id'],
                'patient_id' => $data['patient_id'],
                'assigned_by' => auth('api')->id() ?? Auth::id(),
                'assigned_at' => now(),
                'status' => BedAssignment::StatusPending,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id()
            ]);

            //Add Bed cost to Patient's bill here
            $billing_service->createCharge($query,1);

            //Update Bed Status to In Use
            $bed->assignment_status = Bed::AssignmentStatusInUse;
            $bed->updated_by = auth('api')->id() ?? Auth::id();
            $bed->save();

            //Update Admission Status to Bed Assigned
            $admission->status = AdmissionRequest::StatusBedAssigned;
            $admission->updated_by =auth('api')->id() ?? Auth::id();
            $admission->save();

            DB::commit();
            $this->log_user_activity('Admission Bed Assignment Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Bed Assignment Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_bed_assignment_deactivate($id){
        DB::beginTransaction();

        try{
            $query = BedAssignment::with(['admission', ])->findOrFail($id);

            if ($query->status != BedAssignment::StatusPending) {
                throw new Exception('Only pending bed assignments can be deactivated');
            }

            $query->bed->update([
                'assignment_status' => Bed::AssignmentStatusFree,
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $billing_service = new BedBillingService();
            $billing_service->reverseBedCharge($query, false);

            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            $query->delete();
            
            DB::commit();
            $this->log_user_activity('Admission Bed Assignment Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Bed Assignment Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function admission_bed_assignment_get_all($type, $specific, $detailed, $paginated){
        $query = BedAssignment::query();

        switch($type){
            case 'active':
                $query = $query->whereIn('status', [ BedAssignment::StatusAssigned, BedAssignment::StatusPending]);
                break;
            case 'all':
                $query = $query->withTrashed();
                break;
            case 'released':
                $query = $query->where('status', '=', BedAssignment::StatusReleased)->withTrashed();
                break;
        }

        if (is_array($specific)) {
            if (!empty($specific['admission_id'])) {
                $query = $query->where('admission_id', '=', $specific['admission_id']);
            }
            if(!empty($specific['bed_id'])){
                $query = $query->where('bed_id', '=', $specific['bed_id']);
            }
            if (!empty($specific['patient_id'])) {
                $query = $query->where('patient_id', '=', $specific['patient_id']);
            }
            if(!empty($specific['room_id'])){
                $query = $query->where('room_id', '=', $specific['room_id']);
            }
        }

        $query = $detailed ? $query->with(['bed', 'patient', 'admission']) : $query->select('id', 'bed_id');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function admission_bed_assignment_get_by($type, $id, $detailed){
        try{
            $query = BedAssignment::where('id', '=', $id);
            $query = $detailed ? $query->with(['bed', 'patient', 'admission']) : $query->select('id', 'bed_id');
            $query = $query->firstOrFail();

            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function admission_bed_assignment_release($data, $id){
        DB::beginTransaction();

        try{
            $query = BedAssignment::find($id);
            $query->released_by = auth('api')->id() ?? Auth::id();
            $query->released_at = now();
            $query->status = BedAssignment::StatusReleased;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            $query->bed->update([
                'assignment_status' => Bed::AssignmentStatusFree,
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('Admission Bed Assignment Release', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Bed Assignment Release', $id, false);
            return $e->getMessage();
        }
    }

    public function admission_bed_assignment_update($data, $id){
        DB::beginTransaction();
        try{
            $billing_service = new BedBillingService();
            $num = 1;   
            $query = BedAssignment::with(['bed', 'admission'])->findOrFail($id);

            $admission  = $query->admission;
            $patient_id  = $query->patient_id;

            // Reverse previous charges
            if ($query->status == BedAssignment::StatusPending) {
                $billing_service->reverseBedCharge($query, false);
            }

            elseif ($query->status == BedAssignment::StatusAssigned) {

                $quest = VisitTransaction::where('visit_id', $admission->visit_id)
                    ->where('item_id', $query->bed->roomType->item_id)
                    ->where('patient_id', $patient_id);

                $num = $quest->count();
                $billing_service->reverseBedCharge($query, $data['all_visit'] ?? false);
                //$quest->delete();
            }

            //Release the bed
            $query->bed->update([
                'updated_by' => auth('api')->id() ?? Auth::id(),
                'assignment_status' => Bed::AssignmentStatusFree
            ]);

            $newBed = Bed::lockForUpdate()->findOrFail($data['bed_id']);

            if ($newBed->assignment_status !== Bed::AssignmentStatusFree) {
                throw new Exception('Selected bed is not free');
            }

            $billing_service->createChargeForRoomType($newBed->room_type, $query->patient_id, $admission->visit_id, $num);

            BedAssignmentAudit::create([
                'bed_assignment_id' => $id,
                'old_bed_id'        => $query->bed_id,
                'new_bed_id'        => $newBed->id,
                'changed_by'        => auth('api')->id() ?? Auth::id(),
                'reason'            => $data['reason'] ?? 'Bed change',
            ]);

            $newBed->update([
                'assignment_status' => Bed::AssignmentStatusInUse, 
                'updated_by' => auth('api')->id() ?? Auth::id()
            ]);

            $query->bed_id = $data['bed_id'] ?? $query->bed_id;
            $query->admission_id = $data['admission_id'] ?? $query->admission_id;
            $query->patient_id = $data['patient_id'] ?? $query->patient_id;
            $query->assigned_by = auth('api')->id() ?? Auth::id();
            $query->assigned_at = now();
            $query->status = BedAssignment::StatusAssigned;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();
            
            DB::commit();
            $this->log_user_activity('Admission Bed Assignment Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Bed Assignment Update', $id, false);
            return $e->getMessage();
        }
    }

    
    /*
    --------------------------------------------------------------
    Admission Category Functions
    --------------------------------------------------------------
    */
    public function admission_category_create($data){
        DB::beginTransaction();

        try{
            $query = Category::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? Category::StatusActive,
            ]);

            DB::commit();
            $this->log_user_activity('Admission Category Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Category Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_category_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Category::findOrFail($id);

            if ($query->status != Category::StatusActive) {
                $query->status = Category::StatusInActive;
                $query->deleted_at = now(); 
            }
            else {
                $query->status = Category::StatusActive;
                $query->deleted_at = null; 
            }

            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Category Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Category Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function admission_category_get_all($type, $specific, $detailed, $paginated){
        $query = Category::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
                break;
            case 'all':
                $query = $query->withTrashed();
                break;
            case 'inactive':
                $query = $query->where('status', '=', 0)->withTrashed();
                break;
        }

        if (is_array($specific)) {
            if (!empty($specific['query'])) {
                $search = $specific['query'];
                $query = $query->where('name', 'like', "%$search%");
            }
        }

        $query = $detailed ? $query : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function admission_category_get_by($type, $id, $detailed){
        try{
            $query = Category::where('id', '=', $id);
            $query = $detailed ? $query : $query->select('id', 'name');
            $query = $query->firstOrFail();

            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function admission_category_update($data, $id){
        DB::beginTransaction();
        try{
            $query = Category::find($id);

            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? BedAssignment::StatusAssigned;
            $query->save();
            
            DB::commit();
            $this->log_user_activity('Admission Create Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Create Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    --------------------------------------------------------------
    Admission Request Functions
    --------------------------------------------------------------
    */

    public function admission_request_confirm($id){
        DB::beginTransaction();

        try{ 
            $query = AdmissionRequest::find($id);

            if ($query->status != AdmissionRequest::StatusDraft){
                throw new Exception('Admission request already confirmed.');
            }
            else{    
                $query->status = AdmissionRequest::StatusConfirmed;
                $query->confirmed_by = auth('api')->id() ?? Auth::id();
                $query->confirmed_at = now();
                $query->updated_by = auth('api')->id() ?? Auth::id();

                $query->save();
            }
            
            DB::commit();
            $this->log_user_activity('Admission Room Confirmed', null, false);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Room Confirmed', null, false);
            return $e->getMessage();
        }
    }

    public function admission_request_create($data){
        DB::beginTransaction();

        try{
            $visit = Visit::findOrFail($data['visit_id']);
            $query = AdmissionRequest::create([
                'date' => $data['date'] ?? date('Y-m-d'),
                'visit_id' => $visit->id,
                'branch_id' => $visit->branch_id,
                'consultation_id' => $data['consultation_id'] ?? null,
                'patient_id' => $visit->patient_id,
                'admission_type_id' => $data['admission_type_id'],
                'admission_reason' => $data['admission_reason'],
                'requested_by' => $data['requested_by'],
                'requested_at' => $data['requested_at'] ?? date('Y-m-d'),
                'requested_remark' => $data['requested_remark'],
                'status' => AdmissionRequest::StatusPending,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id()
            ]);

            DB::commit();
            $this->log_user_activity('Admission Request Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Request Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_request_deactivate($id){
        DB::beginTransaction();

        try{
            $query = AdmissionRequest::find($id);
            if ($query->status == AdmissionRequest::StatusDeleted) {
                $query->status = AdmissionRequest::StatusPending;
                $query->deleted_at = null;
                $query->deleted_by = null;
            } 
            else {
                $query->status = AdmissionRequest::StatusPending;
                $query->deleted_at = now();
                $query->deleted_by = auth('api')->id() ?? Auth::id();
            }
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Room Deactivated', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Room Deactivated', null, false);
            return $e->getMessage();
        }
    }

    public function admission_request_get_all($type, $specific, $detailed, $paginated){
        $query = AdmissionRequest::query();

        switch($type){
            
            case 'admitted':
                $query = $query->where('status', '=', AdmissionRequest::StatusAdmitted);
            break;
            case 'bed':
                $query = $query->where('status', '=', AdmissionRequest::StatusBedAssigned);
            break;
            case 'deleted':
                $query = $query->where('status', '=', AdmissionRequest::StatusDeleted);
            break;
            case 'discharged':
                $query = $query->where('status', '=', AdmissionRequest::StatusDischarged);
            break;
            case 'draft':
                $query = $query->where('status', '=', AdmissionRequest::StatusDraft);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'inactive':
                $query = $query->where('status', '=', Room::StatusInActive)->withTrashed();
            break;
            case 'pending':
                $query = $query->where('status', '=', AdmissionRequest::StatusPending);
            break;
            case 'prechecked':
                $query = $query->where('status', '=', AdmissionRequest::StatusPrechecked);
            break;    
        }

        if (is_array($specific)) {
            if (!empty($specific['ward_id'])) {
                $query = $query->where('ward_id', '=', $specific['ward_id']);
            }
            if (!empty($specific['room_id'])) {
                $query = $query->where('room_id', '=', $specific['room_id']);
            }
        }

        $query = $detailed ? $query->with(['bed_assignment.bed.ward', 'bed_assignment.bed.room', 'bed_assignment.bed.room_type', 'patient.user', 'visit']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function admission_request_get_by($type, $id, $detailed){
        try{
            $query = AdmissionRequest::where('id', '=', $id);
            $query = $detailed ? $query->with(['bed_assignment.bed.ward', 'bed_assignment.bed.room', 'bed_assignment.bed.room_type', 'patient.user', 'visit']) : $query->select('id', 'name');
            $query = $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function admission_request_pre_admission_checks_create($data, $id){
        DB::beginTransaction();

        try{
            $query = AdmissionRequest::findOrFail($id);

            if ($query->status !== AdmissionRequest::StatusConfirmed){
                throw new Exception('Admission request not confirmed.');
            }

            foreach ($data['checks'] as $check){
                PreAdmissionCheck::create([
                    'admission_request_id' => $id,
                    'code' => $check['code'],
                    'name' => $check['name'],
                    'meta' => $check['meta'],
                    'notes' => $check['notes'] ?? null,
                    'result' => $check['result'] ?? null,
                    'checked_by' => auth('api')->id() ?? Auth::id(),
                    'checked_at' => date('Y-m-d H:i:s'),
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }

            $query->update([
                'status' => AdmissionRequest::StatusPrechecked,
                'precheck_by' => auth('api')->id() ?? Auth::id(),
                'precheck_at' => now(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            $this->log_user_activity('Admission Pre Admission Checks', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Pre Admission Checks', null, false);
            return $e->getMessage();
        }
    }

    public function admission_request_update($data, $id){
        DB::beginTransaction();

        try{
            $query = AdmissionRequest::findOrFail($id);
            
            if ($query->status != AdmissionRequest::StatusPending){
                throw new Exception('Request has already been processed');
            }

            $visit = Visit::findOrFail($data['visit_id']);

            $query->date = $data['date'] ?? $query->date;
            $query->visit_id = $visit->id;
            $query->branch_id = $visit->branch_id;
            $query->consultation_id = $data['consultation_id'] ?? $query->consultation_id;
            $query->patient_id = $visit->patient_id;
            $query->admission_type_id = $data['admission_type_id'] ?? $query->admission_type_id;
            $query->admission_reason = $data['admission_reason'] ?? $query->admission_reason;
            $query->requested_by = $data['requested_by'] ?? $query->requested_by;
            $query->requested_at = $data['requested_at'] ?? $query->requested_at;
            $query->requested_remark = $data['requested_remark'] ?? $query->requested_remark;
            $query->status = AdmissionRequest::StatusPending;
            $query->created_by = auth('api')->id() ?? Auth::id();
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Request Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Request Updated', $id, false);
            return $e->getMessage();
        }
    }

    /*
    --------------------------------------------------------------
    Admission Room Functions
    --------------------------------------------------------------
    */

    public function admission_room_create($data){
        DB::beginTransaction();

        try{
            $query = Room::create([
                //'name', 'ward_id', 'status', 'created_by', 'updated_by',
                'name' => $data['name'],
                'ward_id' => $data['ward_id'], 
                'room_type_id' => $data['room_type_id'], 
                'status' => $data['status'] ?? Room::StatusActive,
                'description' => $data['description'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id()
            ]);

            DB::commit();
            $this->log_user_activity('Admission Room Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Room Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_room_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Room::find($id);
            if ($query->status == Room::StatusInActive) {
                $query->status = Room::StatusActive;
                $query->deleted_at = null;
                $query->deleted_by = null;
            } 
            else {
                $query->status = Room::StatusInActive;
                $query->deleted_at = now();
                $query->deleted_by = auth('api')->id() ?? Auth::id();
            }
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Room Deactivated', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Room Deactivated', null, false);
            return $e->getMessage();
        }
    }

    public function admission_room_get_all($type, $specific, $detailed, $paginated){
        $query = Room::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', Room::StatusActive);
                break;
            case 'all':
                $query = $query->withTrashed();
                break;
            case 'inactive':
                $query = $query->where('status', '=', Room::StatusInActive)->withTrashed();
                break;
        }

        if (is_array($specific)) {
            if (!empty($specific['ward_id'])) {
                $query = $query->where('ward_id', '=', $specific['ward_id']);
            }
            if (!empty($specific['room_type_id'])) {
                $query = $query->where('room_type_id', '=', $specific['room_type_id']);
            }
        }

        $query = $detailed ? $query->with(['ward', 'room_type']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function admission_room_get_by($type, $id, $detailed){
        try{
            $query = Room::where('id', '=', $id);
            $query = $detailed ? $query->with(['ward', 'room_type']) : $query->select('id', 'name');
            $query = $query->firstOrFail();

            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function admission_room_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Room::findOrFail($id);
            
            $query->name = $data['name'] ?? $query->name;
            $query->ward_id = $data['ward_id'] ?? $query->ward_id;
            $query->room_type_id = $data['room_type_id'] ?? $query->room_type_id;
            $query->status = $data['status'] ?? $query->status;
            $query->description = $data['description'] ?? $query->description;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Room Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Room Updated', $id, false);
            return $e->getMessage();
        }
    }

    /*
    --------------------------------------------------------------
    Admission Room Type Functions
    --------------------------------------------------------------
    */

    public function admission_room_type_create($data){
        DB::beginTransaction();
        try{
            if (!empty($data['item_id'])){
                $item = Item::findOrFail($data['item_id']);
            }
            else{
                $item = Item::create([
                    'average_landing_cost' => $data['landing_cost'] ?? 0.00,
                    'billable' => $data['item']['billable'] ?? 1,
                    'barcode' => $data['item']['barcode'] ?? null,
                    'category_id' => 1,
                    'consumable' => 0,
                    'description' => $data['description'],
                    'image' => null,
                    'is_package' => false,
                    'last_landing_cost' => $data['landing_cost'] ?? 0.00,
                    'name' => $data['name'],
                    'specific_id' => null,
                    'status' => Item::StatusActive,
                    'type_id' => 1,
                    'created_by' => Auth::id() ?? auth('api')->id(),
                    'updated_by' => Auth::id() ?? auth('api')->id(),
                ]);
            }
            $emr_service = EMRService::where('item_id', '=', $item->id)->with(['referenceable'])->first();

            if (!$emr_service){
                $emr_service = EMRService::create([
                    'item_id' => $item->id,
                    'service_type_id' => 1,
                    'referenceable_type' => 'App\Models\EMR\Admission\Service',
                    'referenceable_id' => null,
                    'description' => $data['description'],
                    'status' => EMRService::StatusActive,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
                $admission_service = AdmissionService::create([
                    'service_id' => $emr_service->id,
                    'category_id' => 1,
                    'room_type_id' => null,
                    'status' => AdmissionService::StatusActive,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);

                $emr_service->referenceable_id = $admission_service->id;
                $emr_service->save();
            }
            else{
                $admission_service = $emr_service->reference;
            }
                
            $query = RoomType::create([
                'name' => $data['name'],
                'item_id' => $data['item_id'] ?? $item->id,
                'description' => $data['description'],
                'status' => $data['status'] ?? RoomType::StatusActive,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $admission_service->room_type_id = $query->id;
            $admission_service->save();

            DB::commit();
            $this->log_user_activity('Admission Room Type Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Room Type Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_room_type_deactivate($id){
        DB::beginTransaction();

        try{
            $query = RoomType::find($id);
            if ($query->status == RoomType::StatusInActive) {
                $query->status = RoomType::StatusActive;
                $query->deleted_at = null;
                $query->deleted_by = null;
            } 
            else {
                $query->status = RoomType::StatusInActive;
                $query->deleted_at = now();
                $query->deleted_by = auth('api')->id() ?? Auth::id();
            }
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Room Type Deactivated', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Room Type Deactivated', null, false);
            return $e->getMessage();
        }
    }

    public function admission_room_type_get_all($type, $specific, $detailed, $paginated){
        $query = RoomType::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', RoomType::StatusActive);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'inactive':
                $query = $query->where('status', '=', RoomType::StatusInActive)->withTrashed();
            break;
        }

        if (is_array($specific)) {
            if (!empty($specific['ward_id'])) {
                $query = $query->where('ward_id', '=', $specific['ward_id']);
            }
        }

        $query = $detailed ? $query->with(['admission_service.emr_service.item']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function admission_room_type_get_by_id($type, $id, $detailed){
        try{
            $query = RoomType::where('id', '=', $id);
            $query = $detailed ? $query->with(['admission_service.emr_service.item']) : $query->select('id', 'name');
            $query = $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function admission_room_type_update($data, $id)
    {
        DB::beginTransaction();
        try {
            $roomType = RoomType::lockForUpdate()->findOrFail($id);
            
            if (!empty($data['item_id'])) {
                $item = $this->inventory_item_get_by('id', $data['item_id'], false);
                if (!$item) {
                    throw new Exception('Invalid Item selected for Room Type');
                }
                echo $data['item_id']."Old: ".$item->id;

                $item->name = $data['name'] ?? $item->name;
                $item->description = $data['description'] ?? $item->description;
                $item->updated_by = auth('api')->id() ?? Auth::id();

                $item->save();
            } 
            else {
                $item = $this->inventory_item_create($data['item']);
                echo $data['item_id']."New: ".$item->id;
            }

            $roomType->update([
                'name'        => $data['name']        ?? $roomType->name,
                'description' => $data['description'] ?? $roomType->description,
                'status'      => $data['status']      ?? $roomType->status,
                'item_id'     => $item->id,
                'updated_by'  => auth('api')->id() ?? Auth::id(),
            ]);

            $admissionService = AdmissionService::lockForUpdate()->where('room_type_id', '=', $roomType->id)->first();

            if (!$admissionService) {
                $admissionService = AdmissionService::create([
                    'room_type_id' => $roomType->id,
                    'category_id'  => 1,
                    'status'       => AdmissionService::StatusActive,
                    'created_by'   => auth('api')->id() ?? Auth::id(),
                    'updated_by'   => auth('api')->id() ?? Auth::id(),
                ]);
            } 
            else {
                $admissionService->update([
                    'status'     => $data['status'] ?? $admissionService->status,
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }

            $emrService = EMRService::lockForUpdate()->where('referenceable_type', AdmissionService::class)->where('referenceable_id', $admissionService->id)->first();

            if (!$emrService) {
                $emrService = EMRService::create([
                    'item_id'             => $item->id,
                    'service_type_id'     => 1, // Admission Service
                    'referenceable_type'  => AdmissionService::class,
                    'referenceable_id'    => $admissionService->id,
                    'description'         => $data['description'],
                    'status'              => EMRService::StatusActive,
                    'created_by'          => auth('api')->id() ?? Auth::id(),
                    'updated_by'          => auth('api')->id() ?? Auth::id(),
                ]);

                $admissionService->update([
                    'service_id' => $emrService->id,
                ]);
            } 
            else {
                $emrService->update([
                    'item_id'     => $item->id,
                    'description' => $data['description'] ?? $emrService->description,
                    'status'      => $data['status'] ?? $emrService->status,
                    'updated_by'  => auth('api')->id() ?? Auth::id(),
                ]);
            }

            DB::commit();

            $this->log_user_activity('Admission Room Type Updated', $roomType->id, true);

            
            return RoomType::with(['admission_service.emr_service.item'])->findOrFail($roomType->id);

        } catch (\Throwable $e) {

            DB::rollBack();

            $this->log_user_activity('Admission Room Type Updated', $id, false);

            throw $e; // let API handler format response
        }
    }
        /*
    --------------------------------------------------------------
    Admission Reason Functions
    --------------------------------------------------------------
    */
    public function admission_reason_create($data){
        DB::beginTransaction();

        try{
            $query = Reason::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function admission_reason_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Reason::findOrFail($id);
            
            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_at = now();
            } 
            else{
                $query->status = 1;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }
            
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function admission_reason_get_all($type, $detailed, $paginated){
        $query = Reason::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0)->withTrashed();
            break;
        }

        $query = $detailed ? $query : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query;
    }

    public function admission_reason_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Reason::findOrFail($id);
            
            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
    
    /*
    --------------------------------------------------------------
    Admission Service Functions
    --------------------------------------------------------------
    */
    public function admission_service_create($data){
        DB::beginTransaction();
        try{
            //Check if the item 
            $item = !empty($data['item_id']) ? Item::findOrFail($data['item_id']) : Item::create([
                'average_landing_cost' =>  $data['landing_cost'] ?? 0.00,
                'billable' => $data['billable'] ?? 1,
                'barcode' => null,
                'classification_id' => null,
                'consumable' => 1,
                'description' => $data['description'],
                'image' => null,
                'is_package' => false,
                'last_landing_cost' => $data['landing_cost'] ?? 0.00,
                'name' => $data['name'],
                'specific_id' => null,
                'service_id' => null,
                'status' => Item::StatusActive,
                'type_id' => $data['type_id'] ?? null,
                'unique_id' => null,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(), 
            ]);

            //Check if there is an EMR Service attached to this Item else create.
            $emr_service =  ($item->service_id != null) 
                ? EMRService::findOrFail($item->service_id) 
                : EMRService::create([
                    'item_id' => $item->id,
                    'service_type_id' => 1,
                    'referenceable_type' => 'App\Models\EMR\Admission\Service',
                    'referenceable_id' => null,
                    'description' => $data['description'],
                    'status' => EMRService::StatusActive,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            
            //Check if it is a room type
            if ($data['category_id'] == AdmissionService::TypeAccomodation){
                $room_type = RoomType::create([
                    'name' => $data['name'],
                    'item_id' => $item->id,
                    'description' => $data['description'],
                    'status' => RoomType::StatusActive,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }
            
            //Create the Admission Service
            $query = AdmissionService::create([
                'service_id' => $emr_service->id,
                'category_id' => $data['category_id'],
                'room_type_id' => $room_type->id ?? null,
                'status' => $data['status'] ?? AdmissionService::StatusActive,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $emr_service->referenceable_id = $query->id;
            $emr_service->save();
            
            DB::commit();
            $this->log_user_activity('Admission Service Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Service Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_service_deactivate($id){
        DB::beginTransaction();

        try{
            $query = AdmissionService::findOrFail($id);
            $emr_service = EMRService::where('referenceable_type', '=', 'App\Models\EMR\Admission\Service')->where('referenceable_id', '=', $id)->firstOrFail();
            if ($query->status == AdmissionService::StatusInActive) {
                $query->status = AdmissionService::StatusActive;
                $query->deleted_at = null;
                $query->deleted_by = null;

                $emr_service->status = EMRService::StatusActive;
                $emr_service->deleted_at = null;
                $emr_service->deleted_by = null;
            } 
            else {
                $query->status = AdmissionService::StatusInActive;
                $query->deleted_at = now();
                $query->deleted_by = auth('api')->id() ?? Auth::id();

                $emr_service->status = EMRService::StatusInactive;
                $emr_service->deleted_at = now();
                $emr_service->deleted_by = auth('api')->id() ?? Auth::id();
            }
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            $emr_service->updated_by = auth('api')->id() ?? Auth::id();
            $emr_service->save();

            DB::commit();
            $this->log_user_activity('Admission Ward Deactivated', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Ward Deactivated', null, false);
            return $e->getMessage();
        }
    }

    public function admission_service_get_all($type, $specific, $detailed, $paginated){
        $query = AdmissionService::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', AdmissionService::StatusActive);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'inactive':
                $query = $query->where('status', '=', AdmissionService::StatusInActive)->withTrashed();
            break;
        }

        if (is_array($specific)) {
            if (!empty($specific['branch_id'])) {
                $query = $query->where('branch_id', '=', $specific['branch_id']);
            }
            if (!empty($specific['query'])) {
                $search = $specific['query'];
                $query = $query->where('name', 'like', "%$search%");
            }
        }

        $query = $detailed ? $query->with(['category', 'creator', 'deleter', 'emr_service.item', 'room_type']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function admission_service_get_by($type, $id, $detailed){
        try{
            $query = AdmissionService::where('id', '=', $id);
            $query = $detailed ? $query->with(['category', 'creator', 'deleter', 'emr_service.item', 'room_type']) : $query->select('id', 'name');
            $query = $query->firstOrFail();
            
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function admission_service_update($data, $id){
        DB::beginTransaction();

        try{
            $query = AdmissionService::findOrFail($id);

            $query->service_id = $data['service_id'] ?? $query->service_id;
            $query->category_id = $data['category_id'] ?? $query->category_id;
            $query->room_type_id = $data['room_type_id'] ?? $query->room_type_id;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Ward Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Ward Updated', $id, false);
            return $e->getMessage();
        }
    }

    

    /*
    --------------------------------------------------------------
    Admission Type Functions
    --------------------------------------------------------------
    */
    public function admission_type_create($data){
        DB::beginTransaction();

        try{
            $query = Type::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? '',
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function admission_type_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Type::findOrFail($id);
            
            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_at = now();
            } 
            else{
                $query->status = 1;
                $query->deleted_by = null;
                $query->deleted_at = null;
            }
            
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function admission_type_get_all($type, $detailed, $paginated){
        $query = Type::query();

        switch($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0)->withTrashed();
            break;
        }

        $query = $detailed ? $query : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query;
    }

    public function admission_type_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Type::findOrFail($id);
            
            $query->name = $data['name'] ?? $query->name;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }

    }
    /*
    --------------------------------------------------------------
    Admission Ward Functions
    --------------------------------------------------------------
    */

    public function admission_ward_create($data){
        DB::beginTransaction();
        try{
            $query = Ward::create([
                'name' => $data['name'],
                'branch_id' => $data['branch_id'] ?? null,
                'description' => $data['description'],
                'status' => $data['status'] ?? Ward::StatusActive,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            
            DB::commit();
            $this->log_user_activity('Admission Ward Created', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Ward Created', null, false);
            return $e->getMessage();
        }
    }

    public function admission_ward_deactivate($id){
        DB::beginTransaction();

        try{
            $query = Ward::find($id);
            if ($query->status == Ward::StatusInActive) {
                $query->status = Ward::StatusActive;
                $query->deleted_at = null;
                $query->deleted_by = null;
            } 
            else {
                $query->status = Ward::StatusInActive;
                $query->deleted_at = now();
                $query->deleted_by = auth('api')->id() ?? Auth::id();
            }
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Ward Deactivated', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Ward Deactivated', null, false);
            return $e->getMessage();
        }
    }

    public function admission_ward_get_all($type, $specific, $detailed, $paginated){
        $query = Ward::query()->where('branch_id', '=', request()->cookie('current_branch'));

        switch($type){
            case 'active':
                $query = $query->where('status', '=', Ward::StatusActive);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'inactive':
                $query = $query->where('status', '=', Ward::StatusInActive)->withTrashed();
            break;
        }

        if (is_array($specific)) {
            if (!empty($specific['branch_id'])) {
                $query = $query->where('branch_id', '=', $specific['branch_id']);
            }
            if (!empty($specific['query'])) {
                $search = $specific['query'];
                $query = $query->where('name', 'like', "%$search%");
            }
        }

        $query = $detailed ? $query->with([ 'beds', 'branch', 'rooms',]) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function admission_ward_get_by($type, $id, $detailed){
        try{
            $query = Ward::where('id', '=', $id);
            $query = $detailed ? $query->with([ 'beds', 'branch', 'rooms']) : $query->select('id', 'name');
            $query = $query->firstOrFail();
            
            return $query;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function admission_ward_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Ward::findOrFail($id);

            $query->name = $data['name'] ?? $query->name;
            $query->branch_id = $data['branch_id'] ?? $query->branch_id;
            $query->description = $data['description'] ?? $query->description;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            $query->save();

            DB::commit();
            $this->log_user_activity('Admission Ward Updated', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Admission Ward Updated', $id, false);
            return $e->getMessage();
        }
    }
    
}