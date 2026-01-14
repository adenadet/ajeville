<?php

namespace App\Http\Traits\EMR;

use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Laboratory\Request as LaboratoryRequest;
use App\Models\EMR\Laboratory\Bottle;
use App\Models\EMR\Laboratory\Service;
use App\Models\EMR\Service as EMRService;
use App\Models\Inventory\Item;
use App\Models\Procurement\Vendor;

use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LaboratoryTrait{
    use LogTrait, ItemTrait, VisitTransactionTrait;

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

    public function emr_laboratory_request_create($patient_id, $item_id, $visit_id =null, $consultation_id = null, $date = null, $special = 0){
        DB::beginTransaction();
        
        try{ 
            $transaction = $this->emr_visit_transaction_create($item_id, $patient_id, 1, false, $visit_id);
            if(is_string($transaction)){
                $this->log_user_activity('EMR Laboratory Request Create', true, null);
                return $transaction." Can not create transaction"; 
            }  

            $query = LaboratoryRequest::create([
                // 'result', 'sample_by', 'sample_at', 'sample_remark', 'reported_by', 'reported_at', 'report_remark', 'secondary_report_by', 'secondary_report_at', 'secondary_report_remark', 'approved_by', 'approved_at', 'approval_remark', 'outsourced_type', 'outsourced_to_id', 'outsourced_status_id', 'outsourced_remark', 'insourced_remark', 'insourced_final_remark', 'outsource_result_file', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
                'date' => $date ?? date('Y-m-d'),
                'visit_id' => $visit_id,
                'branch_id' => $visit_id ?? request()->cookie('current_branch'),
                'consultation_id' => $consultation_id,
                'request_type_id' => $data['request_type_id'] ?? null,
                'patient_id' => $patient_id,
                'transaction_id' => $transaction->id,
                'quantity' => 1,
                'item_id' => $item_id,
                'status' => $transaction->status == 1 ? 1 : 0,
                'special' => $special,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $this->log_user_activity('EMR Laboratory Request Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity('EMR Laboratory Request Create', null, false);
            return $e->getMessage();
        }  
    }

    /*
    -----------------------------------------------------------------------
    Laboratory Services
    -----------------------------------------------------------------------
    */
    public function emr_laboratory_service_create($data){
        DB::beginTransaction();

        try{
            $emr_service = EMRService::create([
                'item_id'           => null,
                'service_type_id'   => $data['type_id'] ?? 6,
                'reference_id'      => null,
                'description'       => $data['description'],
                'status'            => EMRService::StatusActive,
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            $service = Service::create([
                'service_id'        => $emr_service->id,
                'category_id'       => $data['category_id'],
                'bottle_type_id'    => $data['bottle_type_id'],
                'specimen_type_id'  => $data['specimen_type_id'],
                'result_template_id'=> $data['result_template_id'],
                'status'            => 1,
                'created_by'        => Auth::id() ?? auth('api')->id(),
                'updated_by'        => Auth::id() ?? auth('api')->id(),
            ]);

            $item = Item::create([
                'name'                  => $data['name'],
                'type_id'               => $data['type_id'] ?? 6,
                'unique_id'             => $this->inventory_generate_unique_id('item'),
                'service_id'            => $data['type_id'],
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

    public function emr_laboratory_service_get_all($type, $specific, $detailed, $paginated){
        $query = Service::query();

        if (is_array($specific)){
            if(!empty($specific['query'])){
                $search = $specific['query'];
                $item_list = Item::where('name', 'LIKE', "%$search%")->pluck('id');
                $service_lists = EMRService::whereIn('item_id', $item_list)->pluck('id');
                $query = $query->whereIn('service_id', $service_lists);
            }

        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'service.item', 'updater']) : $query->with(['service.item']);
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function emr_laboratory_service_update($data, $id){
        DB::beginTransaction();

        try{
            $service = Service::findOrFail($id);
            $service->service_id = $data['service_id'];
            $service->category_id = $data['category_id'];
            $service->bottle_type_id = $data['bottle_type_id'];
            $service->specimen_type_id = $data['specimen_type_id'];
            $service->result_template_id = $data['result_template_id'];
            $service->status = $data['status'] ?? 1;
            $service->updated_by = Auth::id() ?? auth('api')->id();
        
            $service->save();
            
            $emr_service = EMRService::findOrFail($data['service_id']);
            
            $emr_service->item_id = $data['item_id'];
            $emr_service->service_type_id = $data['type_id'] ?? 6;
            $emr_service->reference_id = $id;
            $emr_service->description = $data['description'];
            $emr_service->status = EMRService::StatusActive;
            $emr_service->updated_by =  Auth::id() ?? auth('api')->id();
            $emr_service->save();

            $item = Item::findOrFail($data['item_id']);
            
            $item->name = $data['name'];
            $item->type_id = $data['type_id'] ?? 6;
            $item->service_id = $data['type_id'];
            $item->last_landing_cost = $data['landing_cost'] ?? 0.00;
            $item->average_landing_cost = $data['landing_cost'] ?? 0.00;
            $item->description = $data['description'] ?? null;
            $item->status = 1;
            $item->updated_by = Auth::id() ?? auth('api')->id();
            
            $item->save();

            $this->log_user_activity('EMR Laboratory Service Update', $id, true);
            DB::commit();
            return $emr_service;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('EMR Laboratory Service Update', $id, false);
            return $e->getMessage();
        }
    }
}