<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Laboratory\Request as LaboratoryRequest;
use App\Models\EMR\Laboratory\Bottle;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LaboratoryTrait{
    use LogTrait, TransactionTrait;

    public function emr_laboratory_bottles_create($data){
        DB::beginTransaction();
        
        try{ 
            $query = Bottle::create([
                'name'          => $data['name'],
                'description'   => $data['description'] ?? null,
                'status'        => $data['status'] ?? 1,
                'colour'       => $data['colour'] ?? null,
                'size'         => $data['size'] ?? null,
                'created_by'   => auth('api')->id() ?? Auth::id(),
                'updated_by'   => auth('api')->id() ?? Auth::id(),
            ]);

            $this->log_user_activity('EMR Laboratory Bottle Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Bottle Create', null, false);
            return $e->getMessage();
        }  
    }

    public function emr_laboratory_bottles_deactivate($id){
        DB::beginTransaction();
        
        try{ 
            $query = Bottle::find($id);
            if($query->status == 0){
                    $query::update([
                    'status'       => 0,
                    'updated_by'   => auth('api')->id() ?? Auth::id(),
                    'deleted_by'   => auth('api')->id() ?? Auth::id(),
                    'deleted_at'   => now(),
                ]);
            }
            else{
                $query::update([
                    'status'       => 1,
                    'updated_by'   => auth('api')->id() ?? Auth::id(),
                    'deleted_by'   => null,
                    'deleted_at'   => null,
                ]);
            }
            $this->log_user_activity('EMR Laboratory Bottle Deactivate', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Bottle Deactivate', $id, false);
            return $e->getMessage();
        }  
    }
    
    public function emr_laboratory_bottles_get_all($type='all', $specific, $detailed, $paginated){
        $query = Bottle::query();

        switch($type){
            case 'all':
                $query->withTrashed();
            break;
            case 'active':
                $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query->where('status', '=', 0)->withTrashed();
            break;
        }
        
        $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
        $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function emr_laboratory_bottles_get_by($id, $detailed){
        try{
            $query = Bottle::where('id', '=', $id);
            $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
            $query = $query->firstOrFail();

            return $query;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function emr_laboratory_bottles_update($data, $id){
        DB::beginTransaction();
        
        try{ 
            $query = Bottle::find($id);
            $query::update([
                'name'          => $data['name'] ?? $query->name,
                'description'   => $data['description'] ?? $query->description,
                'status'        => $data['status'] ?? 1,
                'colour'       => $data['colour'] ?? $query->colour,
                'size'         => $data['size'] ?? $query->size,
                'updated_by'   => auth('api')->id() ?? Auth::id(),
            ]);

            $this->log_user_activity('EMR Laboratory Bottle Update', $id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Bottle Update', $id, false);
            return $e->getMessage();
        }  
    }

    /*
    ---------------------------------------------------------
    EMR Laboratory Request Functions
    ---------------------------------------------------------
    */

    public function emr_laboratory_request_create($data, $visit=null, $consultation = null){
        DB::beginTransaction();
        
        try{ 
            $patient_id = $consultation->patient_id ?? ($visit->patient_id ?? $data['patient_id']);
            $transaction = $this->finance_transaction_create($data['item_id'], $patient_id, 1, false, $visit->id);
            if(is_string($transaction)){
                $this->log_activity_user_activity('EMR Laboratory Request Create', true, null);
                return $transaction." Can not create transaction"; 
            }  

            $query = LaboratoryRequest::create([
                // 'result', 'sample_by', 'sample_at', 'sample_remark', 'reported_by', 'reported_at', 'report_remark', 'secondary_report_by', 'secondary_report_at', 'secondary_report_remark', 'approved_by', 'approved_at', 'approval_remark', 'outsourced_type', 'outsourced_to_id', 'outsourced_status_id', 'outsourced_remark', 'insourced_remark', 'insourced_final_remark', 'outsource_result_file', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
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

            $this->log_activity_user_activity('EMR Laboratory Request Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_activity_user_activity('EMR Laboratory Request Create', null, false);
        }  
    }
}