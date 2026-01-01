<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Radiology\Referral;
use App\Models\EMR\Radiology\Request as RadiologyRequest;
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
    public function emr_radiology_request_create($data, $visit=null, $consultation = null){
        DB::beginTransaction();
        
        try{ 
            $patient_id = $consultation->patient_id ?? ($visit->patient_id ?? $data['patient_id']);
            $transaction = $this->finance_transaction_create($data['item_id'], $patient_id, 1, false, $visit->id);
            if(is_string($transaction)){
                $this->log_activity_user_activity('EMR Radiology Request Create', true, null);
                return $transaction." Can not create transaction"; 
            }   
            $query = RadiologyRequest::create([
                'date' => $data['date'],
                'visit_id' => $consultation->visit_id ?? ($visit->id ?? $data['visit_id']),
                'branch_id' => $data['branch_id'],
                'consultation_id' => $consultation->id ?? $data['consultation_id'],
                'request_type_id' => $data['request_type_id'] ?? null,
                'patient_id' => $patient_id,
                'transaction_id' => $transaction->id,
                'quantity' => 1,
                'item_id' => $data['item_id'],
                'status' => $transaction->status == 1 ? 1 : 0,
                'special' => $data['special'],
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
    
}