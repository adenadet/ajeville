<?php
namespace App\Http\Traits\Inventory;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Patient\Package as PatientPackage;
use App\Models\EMR\Patient\PackageItem as PatientPackageItem;
use App\Models\EMR\Settings\ServiceType;
use App\Models\Finance\PriceListItem;
use App\Models\Inventory\Category;
use App\Models\Inventory\Classification;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemType;
use App\Models\Inventory\PackageItem as InventoryPackageItem;
use App\Models\Inventory\Package as InventoryPackage;
use App\Models\Operations\Branch;

use App\Models\EMR\Service as EMRService;
use App\Models\EMR\Admission\Service as AdmissionService;
use App\Models\EMR\Laboratory\Service as LaboratoryService;
use App\Models\EMR\Radiology\Service as RadiologyService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
trait ItemTrait {
    use FileManagerTrait, LogTrait;

    /*
    -----------------------------------------------------------------------------------------------------------
    Item Basic Functions
    -----------------------------------------------------------------------------------------------------------
    */
    public function generateRandomCharacters($length = 10) {
    // Generate cryptographically secure random bytes
        $bytes = random_bytes(ceil($length / 2)); 
        // Convert bytes to a hexadecimal string
        $hexString = bin2hex($bytes);
        // Return the desired length of the string
        return substr($hexString, 0, $length);
    }

    public function inventory_generate_unique_id($type){
        $code = $this->generateRandomCharacters(10);
        switch($type){
            case 'item':
                $prefix = 'PRD';
                $query = Item::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->inventory_generate_unique_id('item');
                }
                else{
                    return $prefix.'-'.$code;
                }
        }      
    }

    public function inventory_item_create($data){
        DB::beginTransaction();
        try{
            $query = Item::create([
                'average_landing_cost' => $data['landing_cost'] ?? 0.00,
                'billable' => $data['billable'] ?? 1,
                'barcode' => $data['barcode'] ?? null,
                'category_id' => is_numeric($data['category_id']) ? $data['category_id'] : (is_string($data['category_id']) ? Category::where('name', '=', $data['category_id'])->pluck('id')->first(): null),
                'classification_id' => is_numeric($data['classification_id']) ? $data['classification_id'] : (is_string($data['classification_id']) ? Classification::where('name', '=', $data['classification_id'])->pluck('id')->first(): null),
                'consumable' => $data['consumable'] ?? 1,
                'description' => $data['description'],
                'image' => $data['image'] != null ? $this->file_upload($data['image'], 'image', '/img/items/', null) : '/img/items/default.png',
                'is_package' => $data['is_package'] ?? false,
                'last_landing_cost' => $data['landing_cost'] ?? 0.00,
                'name' => $data['name'],
                'specific_id' => $data['specific_id'] ?? null,
                'status' => $data['status'] ?? "active",
                'type_id' => $data['type_id'] ?? null,
                'unique_id' => $data['unique_id'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            if ($data['is_package']){

            }

            
            $this->log_user_activity('Item Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Create', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_item_delete($id){
        DB::beginTransaction();
        try{
            $query = Item::where('id', '=', $id)->firstOrFail();
            $query->status =  $query->status == 'Active' ? 'Inactive' : 'Active';
            
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->updated_at = date('Y-m-d H:i:s');
            $query->save();

            DB::commit();
            $this->log_user_activity('Inventory Item Deactivate', $id, true);
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Item Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function inventory_item_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Item::query();
        switch($type){
            case 'active':
                $query = $query->where('status', '=', 'active');
            break;
            case 'admission':
                $query = $query->where('category_id', '=', 15);
            break;
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'category':
                $query = $query->where('category_id', '=', $specific)->withTrashed();
            break;
            case 'classification':
                $query = $query->where('classification_id', '=', $specific);
            break;
            case 'classification_name':
                $classification = Classification::where('name', '=', $specific)->pluck('id')->first();
                $query = $query->where('classification_id', '=', $classification);
            break;
            case 'consumable':
                $query = $query->where('consumable', '=', 1);
            break;
            case 'deleted':
                $query = $query->withTrashed()->where('status', '=', 0);
            break;
            case 'emr_services':
                $query = $query->whereNotNull('service_id');
            break;
            case 'inactive':
                $query = $query->where('status', '=', 'inactive');
            break;
            case 'new':
                $query = $query->query();
            break;
            case 'package':
                $query = $query->whereNotNull('package_items');
            break;
            case 'search_request':
                if(isset($specific['item'])){
                    $name = $specific['item'];
                    $query = $query->where('name', 'LIKE', "%$name%");
                }
                if(isset($specific['brand_id'])){
                    $query = $query->where('brand_id', '=', $specific['brand_id']);
                }
                if(isset($specific['category_id'])){
                    $query = $query->where('category_id', '=', $specific['category_id']);
                }
                if(isset($specific['classification_id'])){
                    $query = $query->where('classification_id', '=', $specific['classification_id']);
                }
            break;
            case 'this_month':
                $query = $query->whereDate('created_at', '>=', date('Y-m-').'01')->whereDate('created_at', '<=', date('Y-m-d'));
            break;
        }

        if (!is_array($specific) && $query !== null){
            $query = $query->where('name', 'LIKE', "%$specific%");
        }

        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['brand', 'category', 'classification', 'item_type', 'service.reference']) : $query->select('id', 'name', 'service_id', 'unique_id');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function inventory_item_get_by($type, $specific, $detailed){
        try{
            $query = Item::query();

            switch($type){
                case 'id':
                    $query =  $query->where('id', '=', $specific);
                break;
                case 'name':
                    $query = $query->where('name', '=', $specific);
                break;
                case 'unique_id':
                    $query = $query->where('unique_id', '=', $specific);
                break;
            }
            $query = $detailed ? $query->with(['category', 'classification', 'item_type', 'service.reference', 'service.service_type']) : $query;
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function inventory_item_search($query, $type, $branch_id, $price_list_id){
        $branch = Branch::find($branch_id);
        echo $branch_id;
        if (!$branch) {throw new Exception("Invalid branch ID");}

        $priceListId = $price_list_id ?? $branch->price_list_id;

        return DB::table('inventory_items')
            ->join('finance_price_list_items', function ($join) use ($priceListId) {
                $join->on('inventory_items.id', '=', 'price_list_items.item_id')->where('finance_price_list_items.price_list_id', '=', $priceListId);
            })
            ->where(function ($q) use ($query) {
                $q->where('inventory_items.name', 'like', "%{$query}%")->orWhere('inventory_items.unique_id', 'like', "%{$query}%");
            })
            ->whereNull('inventory_items.deleted_at') // Soft delete handling
            ->select('inventory_items.id', 'inventory_items.unique_id', 'inventory_items.name', 'finance_price_list_items.price')->get();
    }

    public function inventory_item_update($data, $id){
        DB::beginTransaction();

        try{
            $item = Item::where('id', '=', $id)->orWhere('unique_id', '=', $id)->first();
            
            $item->update([
                'name'                 => $data['name'] ?? $item->name,
                'unique_id'            => is_null($item->unique_id) ? $this->inventory_generate_unique_id('item') : $item->unique_id,
                'average_landing_cost' => $data['average_landing_cost']  ?? $item->average_landing_cost,
                'barcode'              => $data['barcode'] ?? $item->barcode,
                'brand_id'             => $data['brand_id'] ?? $item->brand_id,
                'category_id'          => $data['category_id'] ?? $item->category_id,
                'classification_id'    => $data['classification_id'] ?? $item->classification_id,
                'description'          => $data['description'] ?? $item->description,
                'last_landing_cost'    => $data['last_landing_cost'] ?? $item->last_landing_cost,
                'status'               => $data['status'] ?? "active",
                'type_id'              => $data['type_id'] ?? $item->type_id,
                'updated_by'           => auth('api')->id() ?? Auth::id(),
            ]);
            $item->save();

            if ($item->type_id == 2){

                switch($data['service']['service_type_id']){
                    case 3:
                        $reference_type = 'App\Models\EMR\Admission';
                    break;
                    case 4:
                        $reference_type = 'App\Models\EMR\Consultation\Service';
                    break;
                    case 6:
                        $reference_type = 'App\Models\EMR\Laboratory\Service';
                    break;
                    case 7:
                        $reference_type = 'App\Models\EMR\Radiology\Service';
                    break;
                    case 9:
                        $reference_type = 'App\Models\EMR\Procedure\Service';
                    break;
                }

                if(empty($data['service']['referenceable']['service_id'])){
                    $emr_service = EMRService::create([
                        'item_id' => $item->id,
                        'service_type_id' => $data['service']['service_type_id'],
                        'referenceable_type' => $reference_type,
                        'referenceable_id' => null,
                        'description' => $item->description,
                        'status' => $item->status == 'active' ? EMRService::StatusActive : EMRService::StatusInactive,
                        'created_by' =>  Auth::id() ?? auth('api')->id(),
                        'updated_by' =>  Auth::id() ?? auth('api')->id(),
                    ]);

                    $item->service_id = $emr_service->id;
                    $item->save();
                }
                else{
                    $emr_service = EMRService::findOrFail($data['service']['referenceable']['service_id']);
                    
                    $emr_service->item_id = $item->id;
                    $emr_service->service_type_id = $data['service']['service_type_id'] ?? 6;
                    $emr_service->referenceable_type = $reference_type;
                    $emr_service->description = $data['description'];
                    $emr_service->status = ($item->status == 'active') ? EMRService::StatusActive : EMRService::StatusInactive;
                    $emr_service->updated_by =  Auth::id() ?? auth('api')->id();
                    $emr_service->save();
                }

                switch($data['service']['service_type_id']){
                    case 3: //If it is a Admission issue
                        
                        if(empty($emr_service->referenceable_id)){
                            $admission_service = AdmissionService::create([
                                'service_id' => $emr_service->id,
                                'status' => $data['status'] == 'active' ? 1 : 0,
                                'created_by' => Auth::id() ?? auth('api')->id(),
                                'updated_by' => Auth::id() ?? auth('api')->id(),
                            ]);
                        }
                        else{

                        }
                    break;
                    case 6: //If it is a Laboratory Item 
                        if(empty($emr_service->referenceable_id)){
                            $laboratory_service = LaboratoryService::create([
                                'service_id' => $emr_service->id,
                                'category_id' => $data['service']['referenceable']['category_id'],
                                'bottle_type_id' => $data['service']['referenceable']['bottle_type_id'],
                                'specimen_type_id' => $data['service']['referenceable']['specimen_type_id'],
                                'result_template_id' => $data['service']['referenceable']['result_template_id'],
                                'status' => $data['status'] == 'active' ? 1 : 0,
                                'created_by' => Auth::id() ?? auth('api')->id(),
                                'updated_by' => Auth::id() ?? auth('api')->id(),
                            ]);
                        
                            $emr_service->referenceable_id = $laboratory_service->id;
                            $emr_service->save();
                        }
                        else{
                            $laboratory_service = LaboratoryService::where('id', '=', $emr_service->reference_id)->firstOrFail();
                            $laboratory_service->service_id = $emr_service->id;
                            $laboratory_service->category_id = $data['service']['referenceable']['category_id'];
                            $laboratory_service->bottle_type_id = $data['service']['referenceable']['bottle_type_id'];
                            $laboratory_service->specimen_type_id = $data['service']['referenceable']['specimen_type_id'];
                            $laboratory_service->result_template_id = $data['service']['referenceable']['result_template_id'];
                            $laboratory_service->status = $data['status'] == 'active' ? 1 : 0;
                            $laboratory_service->updated_by = Auth::id() ?? auth('api')->id();
                        
                            $laboratory_service->save();
                        }
                    break;
                    case 7: //If it is a Laboratory Item 
                        if(empty($emr_service->referenceable_id)){
                            $radiology_service = RadiologyService::create([
                                'service_id' => $emr_service->id,
                                'investigation_type_id' => $data['service']['referenceable']['investigation_type_id'] ?? null,
                                'location_id' => $data['service']['referenceable']['location_id'] ?? null,
                                'status' => $data['status'] == 'active' ? 1 : 0,
                                'created_by' => Auth::id() ?? auth('api')->id(),
                                'updated_by' => Auth::id() ?? auth('api')->id(),
                            ]);
                        
                            $emr_service->referenceable_id = $radiology_service->id;
                            $emr_service->save();
                        }
                        else{
                            $radiology_service = RadiologyService::where('id', '=', $emr_service->referenceable_id)->firstOrFail();
                            $radiology_service->service_id = $emr_service->id;
                            $radiology_service->investigation_type_id = $data['service']['referenceable']['investigation_type_id'] ?? $radiology_service->investigation_type_id;
                            $radiology_service->location_id = $data['service']['referenceable']['location_id'] ?? $radiology_service->location_id;
                            $radiology_service->status = $data['status'] == 'active' ? 1 : 0;
                            $radiology_service->updated_by = Auth::id() ?? auth('api')->id();
                        
                            $radiology_service->save();
                        }
                    break;
                    case 9: //If it is a Radiology Item 
                        if(empty($emr_service->referenceable_id)){
                            $radiology_service = RadiologyService::create([
                                'service_id' => $emr_service->id,
                                'investigation_type_id' => $data['service']['referenceable']['investigation_type_id'] ?? null,
                                'location_id' => $data['service']['referenceable']['location_id'] ?? null,
                                'status' => $data['status'] == 'active' ? 1 : 0,
                                'created_by' => Auth::id() ?? auth('api')->id(),
                                'updated_by' => Auth::id() ?? auth('api')->id(),
                            ]);
                        
                            $emr_service->referenceable_id = $radiology_service->id;
                            $emr_service->save();
                        }
                        else{
                            $radiology_service = RadiologyService::where('id', '=', $emr_service->referenceable_id)->firstOrFail();
                            $radiology_service->service_id = $emr_service->id;
                            $radiology_service->investigation_type_id = $data['service']['referenceable']['investigation_type_id'] ?? $radiology_service->investigation_type_id;
                            $radiology_service->location_id = $data['service']['referenceable']['location_id'] ?? $radiology_service->location_id;
                            $radiology_service->status = $data['status'] == 'active' ? 1 : 0;
                            $radiology_service->updated_by = Auth::id() ?? auth('api')->id();
                        
                            $radiology_service->save();
                        }
                    break;
                }    
            }
            DB::commit();
            $this->log_user_activity('Item Update', $id, true);
            return $item;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------------------------------------------
    Item Category Basic Functions
    -----------------------------------------------------------------------------------------------------------
    */
    public function inventory_item_category_create($data){
        DB::beginTransaction();
        try{
            $query = Category::create([
                'name' => $data['name'],
                'primary_category_id' => $data['primary_category_id'] ?? null,
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            $this->log_user_activity('Item Category Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Category Create', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_item_category_delete($id){
        DB::beginTransaction();
        try{
            $query = Category::find($id);
            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();

            DB::commit();
            $this->log_user_activity('Item Category Delete', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Category Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function inventory_item_category_get_all($type, $specific, $detailed, $paginated, $page = 1){
        switch($type){
            case 'active':
                $query = Category::where('status', '=', 1);
            break;
            case 'all':
                $query = Category::withTrashed();
            break;
            case 'deleted':
                $query = Category::withTrashed()->where('status', '=', 0);
            break;
            case 'primary':
                $query = Category::where('primary_category_id', '=', null);
            break;
            case 'secondary':
                $query = Category::where('primary_category_id', '!=', null);
            break;
            case 'type':
                $query = Category::whereIn('type_id', $specific);
            break;
            
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['category', 'creater', 'deleter', 'updater']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }
    
    public function inventory_item_category_get_by($type, $id, $detailed){
        switch ($type){
            case 'id':
                $query = Category::where('id', '=', $id);
            break;
            case 'unique_id':
                $query = Category::where('unique_id', '=', $id);
            break;
        }

        $query = $detailed ? $query->with(['category', 'creater', 'deleter', 'updater']) : $query->select('id', 'name');
        return $query->first();
    }
    
    public function inventory_item_category_update($data, $id){
        DB::beginTransaction();

        try{    
            $query = Category::find($id);

            $query->name = $data['name'];
            $query->primary_category_id = $data['primary_category_id'] ?? null;
            $query->description = $data['description'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            $this->log_user_activity('Item Category Delete', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Category Delete', $id, false);
            return $e->getMessage();
        }

    }

    /*
    -----------------------------------------------------------------------------------------------------------
    Item Classification Basic Functions
    -----------------------------------------------------------------------------------------------------------
    */
    public function inventory_item_classification_create($data){
        DB::beginTransaction();

        try{    
            $query = Classification::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            $this->log_user_activity('Item Classification Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Classification Create', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_item_classification_delete($id){
        DB::beginTransaction();

        try{    
            $query = Classification::find($id);

            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');

            $query->save();
            $this->log_user_activity('Item Classification Delete', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Classification Delete', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_item_classification_get_all($type, $specific, $detailed, $paginated, $page = 1){
        switch($type){
            case 'active':
                $query = Classification::where('status', '=', 1);
            break;
            case 'all':
                $query = Classification::withTrashed();
            break;
            case 'inactive':
                $query = Classification::withTrashed()->where('status', '=', 0);
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }
    
    public function inventory_item_classification_get_by($type, $id, $detailed){
        switch ($type){
            case 'id':
                $query = Classification::where('id', '=', $id);
            break;
            case 'unique_id':
                $query = Classification::where('unique_id', '=', $id);
            break;
        }

        $query = $detailed ? $query->with(['creater', 'deleter', 'updater']) : $query->select('id', 'name');
        return $query->first();
    }

    public function inventory_item_classification_update($data, $id){
        DB::beginTransaction();

        try{    
            $query = Classification::find($id);

            $query->name = $data['name'];
            $query->description = $data['description'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            $this->log_user_activity('Item Type Delete', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Type Delete', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------------------------------------------
    Package Basic Functions
    -----------------------------------------------------------------------------------------------------------
    */

    public function inventory_package_item_create($package_id, $data){
        $query = InventoryPackageItem::create([
            'package_id' => $package_id,
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
        ]);
    }

    public function inventory_package_assign_to_patient($package_id, $patient_id){
        $patient_package = PatientPackage::create(['patient_id' => $patient_id, 'package_id' => $package_id, 'status' => 1,]);
        if(!$patient_package){return 'Error creating patient package';}
        $package_items = InventoryPackageItem::where('package_id', '=', $package_id)->get();
        
        foreach($package_items as $package_item){
            $patient_package_item = PatientPackageItem::create([
                'patient_package_id' => $patient_package->id,
                'item_id' => $package_item->item_id,
                'quantity' => $package_item->quantity,
                'balance' => $package_item->quantity,
            ]);
            if(!$patient_package_item){
                return 'Error creating patient package item';
            }
        }
    }

    /*
    -----------------------------------------------------------------------------------------------------------
    Type Basic Functions
    -----------------------------------------------------------------------------------------------------------
    */
    public function inventory_item_type_create($data){
        DB::beginTransaction();

        try{    
            $query = ItemType::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            $this->log_user_activity('Item Type Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Type Create', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_item_type_delete($id){
        DB::beginTransaction();

        try{    
            $query = ItemType::find($id);

            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');

            $query->save();
            $this->log_user_activity('Item Type Delete', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Type Delete', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_item_type_get_all($type, $specific, $detailed, $paginated, $page = 1){
        switch($type){
            case 'active':
                $query = ItemType::where('status', '=', 1);
            break;
            case 'all':
                $query = ItemType::withTrashed();
            break;
            case 'inactive':
                $query = ItemType::withTrashed()->where('status', '=', 0);
            break;
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }
    
    public function inventory_item_type_get_by($type, $id, $detailed){
        switch ($type){
            case 'id':
                $query = ItemType::where('id', '=', $id);
            break;
            case 'unique_id':
                $query = ItemType::where('unique_id', '=', $id);
            break;
        }

        $query = $detailed ? $query->with(['creater', 'deleter', 'updater']) : $query->select('id', 'name');
        return $query->first();
    }

    public function inventory_item_type_update($data, $id){
        DB::beginTransaction();

        try{    
            $query = ItemType::find($id);

            $query->name = $data['name'];
            $query->description = $data['description'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = Auth::id() ?? auth('api')->id();

            $query->save();

            DB::commit();
            $this->log_user_activity('Item Type Delete', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Item Type Delete', $id, false);
            return $e->getMessage();
        }
    }

    private function inventory_item_unique_id_create(){
        return strtoupper(config('app.short_code').'-'.dechex(time()));
    }
}