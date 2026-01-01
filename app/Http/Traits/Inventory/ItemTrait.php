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
            case 'emr_services':
                //$service_types = ServiceType::where('queueable', '=', 1)->pluck('id');
                //$query = $query->whereIn('classification_id', $service_types);
                $query = $query->whereNull('classification_id');
            break;
            case 'consumable':
                $query = $query->where('consumable', '=', 1);
            break;
            case 'deleted':
                $query = $query->withTrashed()->where('status', '=', 0);
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
        $query = $detailed ? $query->with(['brand', 'category', 'classification', 'item_type', 'service']) : $query->select('id', 'name', 'unique_id');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function inventory_item_get_by($type, $specific, $detailed){
        switch($type){
            case 'id':
                $query =  Item::where('id', $specific);
            break;
            case 'name':
                $query = Item::where('name', $specific);
            break;
            case 'unique_id':
                $query = Item::where('unique_id', $specific);
            break;
        }

        $query = $detailed ? $query->with(['category', 'classification', 'item_type', 'service'])->first() : $query->first();

        return $query;
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
            $item = Item::where('id', '=', $id)->first();
            
            $item->name                 = $data['name'];
            $item->average_landing_cost = $data['average_landing_cost'];
            $item->barcode              = $data['barcode'];
            $item->brand_id             = $data['brand_id'];
            $item->category_id          = $data['category_id'];
            $item->classification_id    = $data['classification_id'];
            $item->description          = $data['description'];
            $item->last_landing_cost    = $data['last_landing_cost'];
            $item->status               = $data['status'] ?? "active";
            $item->type_id              = $data['type_id'];
            $item->unique_id            = $data['unique_id'];
            $item->updated_by           = auth('api')->id();
            
            $item->save();
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