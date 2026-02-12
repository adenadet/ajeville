<?php

namespace App\Http\Traits\Operations;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\UMS\LogTrait;

use App\Models\EMR\Admission\Service as AdmissionService;
use App\Models\EMR\Consultation\Service as ConsultationService;
use App\Models\EMR\Laboratory\Service as LaboratoryService;
use App\Models\EMR\Radiology\Service as RadiologyService;

use App\Models\EMR\Service;
use App\Models\EMR\Settings\ServiceType;
use App\Models\EMR\Settings\Category;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchModule;
use App\Models\Operations\Branch;
use App\Models\Operations\Consumable;
use App\Models\UMS\Employee;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

trait ServiceTrait{
    use ItemTrait, LogTrait;
    //Category Operation
    public function operation_emr_category_get_all($main, $sub_cart, $detailed, $paginated, $page){
        $query = (!is_null($sub_cart)) ? Category::where('main_class_id', '=', $main)->where('primary_category_id', '=', $sub_cart) : Category::where('main_class_id', '=', $main); 
        $query = $detailed ? $query->with('creator', 'updater') : $query->select('id', 'name');
        $categories = $paginated ? $query->paginate(50) : $query->get();

        return $categories;
    }

    //Service Operation
    public function operation_service_create($data){
        DB::beginTransaction();
        try{
            $reference = null;
            //echo $data['item']['name']."\n";
            $item = Item::create([
                'average_landing_cost' => $data['item']['landing_cost'] ?? 0.00,
                'billable' => $data['item']['billable'] ?? 1,
                'barcode' => $data['item']['barcode'] ?? null,
                'type_id' => $data['item']['service_type_id'],
                'consumable' => 1,
                'description' => $data['item']['description'],
                'last_landing_cost' => $data['item']['landing_cost'] ?? 0.00,
                'name' => $data['item']['name'],
                'specific_id' => null,
                'status' => $data['item']['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            
            $service_type = ServiceType::find($data['item']['service_type_id']);
                    
            switch(strtolower($service_type->name)){
                case 'admission':
                    $reference = AdmissionService::create([
                        'name' => $data['name'],
                        'unique_id' => 'AS-'.strtotime(date('Y-m-d H:i:s')),
                        'item_id' => $item->id,
                        'category_id' => $data['category_id'] ?? NULL,
                        'sub_category_id' => $data['sub_category_id'] ?? NULL,
                        'description' => $data['description'],
                        'status' => 1,
                        'created_by' => auth('api')->id() ?? Auth::id(),
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]);
                break;
                case 'consultation':
                    $reference = ConsultationService::create([
                        'item_id' => $item->id,
                        'consultant_id' => $data['service']['consultant_id'],
                        'specialty_id' => $data['service']['specialty_id'],
                        'status' => $data['item']['status'] ?? 1,
                        'is_default' => 1,
                        'created_by' => auth('api')->id() ?? Auth::id(),
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]);
                break;    
                case 'laboratory':
                    $reference = LaboratoryService::create([
                        'name' => $data['name'],
                        'unique_id' => 'LS-'.strtotime(date('Y-m-d H:i:s')),
                        'item_id' => $item->id,
                        'bottle_type_id' => $data['bottle_type'],
                        'specimen_id' => $data['specimen_id'],
                        'result_template_id' => $data['result_tempalte_id'],
                        'description' => $data['description'],
                        'status' => 1,
                        'created_by' => auth('api')->id() ?? Auth::id(),
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]);
                break;
                case 'radiology':
                    $reference = RadiologyService::create([
                        'name' => $data['name'],
                        'unique_id' => 'RS-'.strtotime(date('Y-m-d H:i:s')),
                        'item_id' => $item->id,
                        'bottle_type_id' => $data['bottle_type'],
                        'specimen_id' => $data['specimen_id'],
                        'result_template_id' => $data['result_tempalte_id'],
                        'description' => $data['description'],
                        'status' => 1,
                        'created_by' => auth('api')->id() ?? Auth::id(),
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]);
                break;
            }

            //echo $reference->id;
            //Create Service
            $service = Service::create([
                'item_id' => $item->id,
                'service_type_id' => $data['item']['service_type_id'],
                'reference_id' => $reference->id ?? null,
                'description' => $data['item']['description'],
                'status' => 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            
            $this->log_user_activity( 'Operation Service Create', $service->id, true);
            DB::commit();

            return $service;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Service Create', null, false);
            return $e->getMessage();
        }
    }

    public function operation_service_get_all($type, $specific, $detailed, $paginated){
        $query = Service::query();

        switch ($type){
            case 'admission':
                $service_type = ServiceType::where('name', '=', 'Admission')->firstOrFail(); 
                $query = $query->where('service_type_id', '=', $service_type->id);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'consultation':
                $service_type = ServiceType::where('name', '=', 'Consultation')->firstOrFail(); 
                $query = $query->where('service_type_id', '=', $service_type->id);
            break;
            case 'dialysis':
                $service_type = ServiceType::where('name', '=', 'Dialysis')->firstOrFail(); 
                $query = $query->where('service_type_id', '=', $service_type->id);
            break;
            case 'laboratory':
                $service_type = ServiceType::where('name', '=', 'Laboratory')->firstOrFail(); 
                $query = $query->where('service_type_id', '=', $service_type->id);
            break;
            case 'physiotherapy':
                $service_type = ServiceType::where('name', '=', 'Physiotherapy')->firstOrFail(); 
                $query = $query->where('service_type_id', '=', $service_type->id);
            break;
            
            case 'radiology':
                $service_type = ServiceType::where('name', '=', 'Admission')->firstOrFail(); 
                $query = $query->where('service_type_id', '=', $service_type->id);
            break;
        }

        if(is_array($specific)){
            if (!empty($specific['status'])){
                $query = $query->where('status', '=', $specific['status']);
            }
            if (!empty($specific['query'])){
                //$query = $query->where('status', '=', $specific['status']);
            }
        }

        $query = $detailed ? $query->with(['item', 'service_type', 'creator', 'deleter', 'updater']) : $query->select('id', 'item_id', 'service_type_id', 'referenceable_id', 'description', 'status')->with(['item']);
        $services = $paginated ? $query->paginate(50) : $query->get();
        
        return $services;
    }

    public function operation_service_update($data, $id){
        DB::beginTransaction();
        try{
            switch ($data['type']){
                case 'admission':
                    $service = AdmissionService::find($id);
                    $item = $this->inventory_item_update_item($data, $service->item_id);
                
                    $service->name = $data['name'];
                    $service->category_id = $data['category_id'] ?? NULL;
                    $service->sub_category_id = $data['sub_category_id'] ?? NULL;
                    $service->description = $data['description'];
                    $service->status = $data['status'] ?? 1;
                    $service->updated_by = auth('api')->id() ?? Auth::id();

                    $service->save();
                    
                break;
                case 'laboratory':
                    $service = LaboratoryService::find($id);
                    $item = $this->inventory_item_update_item($data, $service->item_id);
                
                    $service->name = $data['name'];
                    $service->bottle_type_id = $data['bottle_type'];
                    $service->specimen_id = $data['specimen_id'];
                    $service->result_template_id = $data['result_tempalte_id'];
                    $service->description = $data['description'];
                    $service->status = $data['status'] ?? 1;
                    $service->created_by = auth('api')->id() ?? Auth::id();
                    
                    $service->save();
                    
                break;
                case 'radiology':
                    //$query = $detailed ? RadiologyService::with(['items', 'creator', 'updatee']) : RadiologyService::with(['items']);
                break;
            }

        
            
            return $data;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Service Create', false, null);
        }
    }
    


    //Service Type operations
    public function operation_service_type_create($request){
        DB::beginTransaction();
        try{
            $service_type = ServiceType::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'status' => $request->input('status') ?? 1,
                'created_by' => auth('api')->id(),
                'updated_by' => auth('api')->id(),
            ]);
            
            $this->log_user_activity( 'Operation Branch Create', false, null);
            DB::commit();

            return $service_type;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Branch Create', false, null);
        }
        
    }

    public function operation_service_type_deactivate($id){
        DB::beginTransaction();
        try{
            $service = ServiceType::find($id);

            $service->status = $service->status == 0 ? 1 : 0;
            $service->updated_by = auth('api')->id();
            
            $service->save();

            DB::commit();
            $this->log_user_activity( 'Operation Service Type Deactivate', true, null);
        
            return $service;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Service Type Deactivate', false, null);
        }
        
    }

    public function operation_service_type_get_all($type, $specific, $detailed, $paginated){
        $query = ServiceType::query();

        switch($type){
            case 'queueable':
                $query = $query->whereNotNull('queueable');
            break;
        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'updater', 'services.item']) : $query->select('id', 'name'); 
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(10) : $query->get();

        return $query;          
    }

    public function operation_service_type_get_by_id($id){
        $service_type = ServiceType::where('id', '=', $id)->with('items')->first();
        return $service_type;          
    }

    public function operation_service_type_update($request, $id){
        DB::beginTransaction();
        try{
            $service_type = ServiceType::where('id', '=', $id)->first();
            
            $service_type->name = $request->input('name');
            $service_type->status = $request->input('status') ?? 1;
            $service_type->updated_by = auth('api')->id();

            $service_type->save();
            $this->log_user_activity( 'Operation Service Type Update', true, null);
            DB::commit();

            return $service_type;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Service Type Update', false, null);
            return $e->getMessage();
        }
    }
}
