<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Radiology\InvestigationType;
use App\Models\EMR\Radiology\Referral;
use App\Models\EMR\Radiology\Request as RadiologyRequest;
use App\Models\EMR\Radiology\Service;
use App\Models\EMR\Service as EMRService;
use App\Models\Inventory\Item;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait RadiologyTrait{
    use FileManagerTrait, LogTrait, TransactionTrait;

    public function emr_radiology_referral_create($data){
        DB::beginTransaction();
        try{
            $query = Referral::create([
                'request_id' => $data['request_id'],
                'date' => $data['date'] ?? date('Y-m-d'),
                //'transaction_id' => $data[],
                'source_branch_id' => $data['source_branch_id'],
                'destination_branch_id' => $data['destination_branch_id'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'outsourced_type' => $data['outsource_type'],
                'status' => $data['status'] ?? 0,
                'outsourced_status_id' => 1,
                'outsourced_remark' => $data['outsourced_remark'],
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            
            DB::commit();
            $this->log_user_activity('Radiology Referral Create', $query->id, true);
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('Radiology Referral Create', null, false);
            return $e->getMessage();
        }
    }
    
    public function emr_radiology_referral_get_all($type, $specific, $quest, $detailed, $paginated, $page){
        //$branch_id = request->cookie
        switch($type){
            case 'in':
                $query = Referral::where('destination_branch_id', '=', $branch_id ?? 1);
            break;
            case 'out':
                $query = Referral::where('source_branch_id', '=', $branch_id ?? 1);
            break;
        }

        if (is_array($specific)){

        }
        else{
            $query = isset($specific) && !(is_null($specific)) && ($specific != '') ? $query->where('status', '=', $specific) : $query; 
        }

        if (isset($quest)){
            //Search for Patients with name like $quest

            //Search for $query that have unique_id like $quest

            //
        }

        $query = $detailed ? $query->with(['patient.user', 'source_branch', 'destination_branch', 'vendor', 'creator', 'receiver'])->orderBy('date', 'DESC') : $query->select('id', 'unique_id', 'date');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }
    
    public function emr_radiology_investigation_type_create($data){
        DB::beginTransaction();

        try{
            $query = InvestigationType::create([
                'name' => $data['name'],
                'status' => $data['status'],
            ]);

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function emr_radiology_investigation_type_deactivate($id){
        DB::beginTransaction();

        try{
            $query = InvestigationType::where('id', '=', $id)->withTrashed()->firstOrFail();

            if (!is_null($query->deleted_at)){
                $query->deleted_at = null;
            }
            else{
                $query->deleted_at = date('Y-m-d H:i:s');
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

    public function emr_radiology_investigation_type_get_all($type, $specific, $detailed, $paginated){
        $query = InvestigationType::query();
        $query = $detailed ? $query : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_radiology_investigation_type_get_by($type, $id, $detailed){
        try{
            $query = InvestigationType::where('id', '=', $id);
            $query = $detailed ? $query : $query->select('id', 'name');
            
            return $query->firstOrFail();
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function emr_radiology_investigation_type_update($data, $id){
        DB::beginTransaction();

        try{
            $query = InvestigationType::where('id', '=', $id)->firstOrFail();

            $query->name = $data['name'] ?? $query->name;
            $query->status = $data['status'] ?? $query->status;
            $query->save();

            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public function emr_radiology_request_approve_result($data, $id){
        DB::beginTransaction();

        try{    
            $query = RadiologyRequest::find($id);

            $query->approval_remark = $data['remark'];
            $query->approved_by = Auth::id() ?? auth('api')->id();
            $query->approved_at = date('Y-m-d H:i:s');
            $query->status = 10;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            $this->log_activity_user_activity('EMR Radiology Request Result Approval', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity('EMR Radiology Request Result Approval', $id, false);
            return $e->getMessage();
        }
    }
    public function emr_radiology_request_create($patient_id, $item_id, $visit_id=null, $consultation_id = null, $date = null, $special = 0){
        DB::beginTransaction();
        
        try{ 
            $transaction = $this->emr_visit_transaction_create($item_id, $patient_id, 1, false, $visit_id);
            if(is_string($transaction)){
                $this->log_user_activity('EMR Laboratory Request Create', true, null);
                return $transaction." Can not create transaction"; 
            }   
            $query = RadiologyRequest::create([
                'date' => $date,
                'visit_id' => $visit_id,
                'branch_id' => $visit_id ?? request()->cookie('current_branch'),
                'consultation_id' => $consultation_id,
                'request_type_id' => $data['request_type_id'] ?? null,
                'patient_id' => $patient_id,
                'transaction_id' => $transaction->id,
                'quantity' => 1,
                'item_id' => $item_id,
                'status' => RadiologyRequest::StatusBooked,
                'special' => $special,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $this->log_activity_user_activity('EMR Radiology Request Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity('EMR Radiology Request Create', null, false);
        }  
    }

    public function emr_radiology_request_collect_sample($data, $id){
        DB::beginTransaction();

        try{    
            $query = RadiologyRequest::find($id);

            $query->sample_by = Auth::id() ?? auth('api')->id();
            $query->sample_at = date('Y-m-d H:i:s');
            $query->sample_remark = $data['sample_remark'];
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            $this->log_activity_user_activity('EMR Radiology Request Sample Collection', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity('EMR Radiology Request Sample Collection', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_radiology_request_get_all($type, $specific, $detailed, $paginated, $page ){
        switch($type){
            case 'all':
                $query = RadiologyRequest::withTrashed();
                if (isset($specific['status'])){$query->where('status', '=', $specific['status']);}
                if (isset($specific['start_date'])){$query->whereDate('date', '>=', $specific['start_date']);}
                if (isset($specific['end_date'])){$query->whereDate('date', '>=', $specific['end_date']);}
            break;
            case 'status':
                $query = RadiologyRequest::where('status', '=', $specific);
            break;  
        }

        $query = $detailed ? $query->with(['patient', 'item.category.primary_category']) : $query->select('id', 'unique_id', 'item_id');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function emr_radiology_request_get_by($type, $id, $detailed){
        switch($type){
            case 'id':
                $query = RadiologyRequest::where('id', '=', $id);
            break;
            case 'unique_id':
                $query = RadiologyRequest::where('unique_id', '=', $id);
            break;
        }
        $query = $detailed ? $query->with(['patient', 'item.category.primary_category', 'creator', 'updater', 'approver', 'sampler', 'reporter', 'secondary_reporter', 'outsourced_to', 'outsourced_branch', 'sourced_from']) : $query->with(['patient', 'item.category.primary_category']);
        return $query->first();
    }

    public function emr_radiology_request_outsource($data, $id){
        DB::beginTransaction();

        try{    
            $query = RadiologyRequest::find($id);

            $query->outsurced_by = Auth::id() ?? auth('api')->id();
            $query->outsurced_remark = $data['outsourced_remark'];
            $query->outsurced_to_id = $data['outsourced_to_id'];
            $query->outsurced_type = $data['outsourced_type'];
            $query->outsurced_status_id = 0;
            $query->status = 7; 
            $query->outsurced_at = date('Y-m-d H:i:s');
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            $this->log_activity_user_activity('EMR Radiology Request Sample Outsource', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity('EMR Radiology Request Sample Outsource', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_radiology_request_outsource_result($data, $id){
        DB::beginTransaction();

        try{    
            $file = $this->file_upload($data['file'], $data['file_type'], 'uploads/rad_results/', $id);
            
            $query = RadiologyRequest::find($id);
            $query->outsourced_report = $data['report'];
            $query->outsourced_report_file = $file ?? null;
            $query->outsourced_status_id = 1;
            $query->status = 8;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            $this->log_activity_user_activity('EMR Radiology Request Sample Outsource', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity('EMR Radiology Request Sample Outsource', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_radiology_request_report_result($data, $id){
        //this is links to the HL7 project
    }

    public function emr_radiology_service_create($data){
        DB::beginTransaction();

        try{
            $emr_service = EMRService::create([
                'item_id'           => null,
                'service_type_id'   => $data['type_id'] ?? 6,
                'reference_id'      => null,
                'description'       => $data['description'],
                'status'            => EMRService::StatusActive ?? 1,
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            $service = Service::create([
                'service_id'        => $emr_service->id,
                'type_id'           => $data['radiology_type_id'],
                'location_id'       => $data['location_id'] ?? null,
                'status'            => 1,
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            $item = Item::create([
                'name'                  => $data['name'],
                'type_id'               => $data['type_id'] ?? 7,
                'unique_id'             => $this->inventory_generate_unique_id('item'),
                'service_id'            => $emr_service->id,
                'last_landing_cost'     => $data['landing_cost'] ?? 0.00,
                'average_landing_cost'  => $data['landing_cost'] ?? 0.00,
                'description'           => $data['description'] ?? null,
                'status'                => 1,
                'created_by'            => Auth::id() ?? auth('api')->id(),
                'updated_by'            => Auth::id() ?? auth('api')->id(),
            ]);

            $emr_service->reference_id = $service->id;
            $emr_service->item_id = $item->id;

            $emr_service->save();

            $this->log_user_activity('EMR Laboratory Service Create', $service->id, true);
            DB::commit();
            return $emr_service;

        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Laboratory Service Create', null, false);
            return $e->getMessage();
        }
    }

    public function emr_radiology_service_deactivae($id){}

    public function emr_radiology_service_get_all($type, $specific, $detailed, $paginated){}

    public function emr_radiology_service_get_by($type, $id, $detailed){}

    public function emr_radiology_service_update($data, $id){}
    
}