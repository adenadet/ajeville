<?php
namespace App\Http\Traits\Inventory;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Inventory\Brand;
use App\Models\Inventory\Category;
use App\Models\Inventory\Classification;
use App\Models\Inventory\ItemType;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

trait SettingsTrait {
    use FileManagerTrait, LogTrait;

    /*
    --------------------------------------------------------
    Inventory Settings Brands Functions
    --------------------------------------------------------
    */
    public function inventory_settings_brand_create($data){
        DB::beginTransaction();

        try{
            $brand = Brand::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            DB::commit();
            $this->log_user_activity('Inventory Brand Create', $brand->id, true); 
            return $brand;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Brand Create', null, false);
            return $e->getMessage();    
        }
    }

    public function inventory_settings_brand_deactivate($id){
        DB::beginTransaction();

        try{
            $brand = Brand::find($id);
            
            $brand->status = $brand->status == 'active' ? 'inactive' : 'active';
            $brand->updated_by = auth('api')->id() ?? Auth::id();

            $brand->save();
            
            DB::commit();
            $this->log_user_activity('Inventory Brand Deactivated', $id, true);
            return $brand;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Brand Deactivated', $id, false);
            return $e->getMessage();
        }
    }

    public function inventory_settings_brand_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'active':
                $query = Brand::where('status', '=', 'active');
            break;
            case 'inactive':
                $query = Brand::where('status', '=', 'inactive');
            break;
            default:
                $query = Brand::query();
        }

        $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
        $query->orderBy('name', 'ASC');
        $query = $paginated? $query->paginate(20) : $query->get();
        
        return $query;
    }

    public function inventory_settings_brand_get_by($type, $id, $detailed){
       $query = Brand::where('id', '=', $id);
       $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
       $query->first();

       return $query;
    }

    public function inventory_settings_brand_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Brand::findOrFail($id);

            $query->name =$data['name'];
            $query->description =$data['description'] ?? null;
            $query->status =$data['status'] ?? 'active';
            $query->updated_by =auth('api')->id() ?? Auth::id();
            
            $query->save();
            DB::commit();
            $this->log_user_activity('Inventory Brand Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Brand Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    --------------------------------------------------------
    Inventory Settings Categorys Functions
    --------------------------------------------------------
    */
    public function inventory_settings_category_create($data){
        DB::beginTransaction();

        try{
            $brand = Category::create([
                'name'                  => $data['name'],
                'description'           => $data['description'] ?? null,
                'primary_category_id'   => $data['primary_category_id'] ?? null,
                'type_id'               => $data['type_id'] ?? null,
                'status'                => $data['status'] ?? 1,
                'created_by'            => auth('api')->id() ?? Auth::id(),
                'updated_by'            => auth('api')->id() ?? Auth::id(),
            ]);
            DB::commit();
            $this->log_user_activity('Inventory Category Create', $brand->id, true); 
            return $brand;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Category Create', null, false);
            return $e->getMessage();    
        }
    }

    public function inventory_settings_category_deactivate($id){
        DB::beginTransaction();

        try{
            $brand = Category::find($id);
            
            $brand->status = $brand->status == 1 ? 0 : 1;
            $brand->updated_by = auth('api')->id() ?? Auth::id();

            $brand->save();
            
            DB::commit();
            $this->log_user_activity('Inventory Category Deactivated', $id, true);
            return $brand;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Category Deactivated', $id, false);
            return $e->getMessage();
        }
    }

    public function inventory_settings_category_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'active':
                $query = Category::where('status', '=', 1);
            break;
            case 'inactive':
                $query = Category::where('status', '=', 0);
            break;
            default:
                $query = Category::query();
        }

        $query = $detailed ? $query->with(['category', 'classification', 'creater', 'item_type', 'updater', 'deleter']) : $query->select('id', 'name');
        $query->orderBy('name', 'ASC');
        $query = $paginated? $query->paginate(20) : $query->get();
        
        return $query;
    }

    public function inventory_settings_category_get_by($type, $id, $detailed){
       $query = Category::where('id', '=', $id);
       $query = $detailed ? $query->with(['category', 'classification', 'creater', 'item_type', 'updater', 'deleter']) : $query->select('id', 'name');
       $query->first();

       return $query;
    }

    public function inventory_settings_category_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Category::findOrFail($id);

            $query->name = $data['name'];
            $query->description = $data['description'] ?? null;
            $query->primary_category_id = $data['primary_category_id'] ?? null;
            $query->type_id = $data['type_id'] ?? null;
            $query->status = $data['status'] ?? 1;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();
            DB::commit();
            $this->log_user_activity('Inventory Category Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Category Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    --------------------------------------------------------
    Inventory Settings Classifications Functions
    --------------------------------------------------------
    */
    public function inventory_settings_classification_create($data){
        DB::beginTransaction();

        try{
            $brand = Classification::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'active',
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            DB::commit();
            $this->log_user_activity('Inventory Classification Create', $brand->id, true); 
            return $brand;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Classification Create', null, false);
            return $e->getMessage();    
        }
    }

    public function inventory_settings_classification_deactivate($id){
        DB::beginTransaction();

        try{
            $brand = Classification::find($id);
            
            $brand->status = $brand->status == 'active' ? 'inactive' : 'active';
            $brand->updated_by = auth('api')->id() ?? Auth::id();

            $brand->save();
            
            DB::commit();
            $this->log_user_activity('Inventory Classification Deactivated', $id, true);
            return $brand;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Classification Deactivated', $id, false);
            return $e->getMessage();
        }
    }

    public function inventory_settings_classification_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'active':
                $query = Classification::where('status', '=', 1);
            break;
            case 'inactive':
                $query = Classification::where('status', '=', 0);
            break;
            default:
                $query = Classification::query();
        }

        $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
        $query->orderBy('name', 'ASC');
        $query = $paginated? $query->paginate(20) : $query->get();
        
        return $query;
    }

    public function inventory_settings_classification_get_by($type, $id, $detailed){
       $query = Classification::where('id', '=', $id);
       $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
       $query->first();

       return $query;
    }

    public function inventory_settings_classification_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Classification::findOrFail($id);

            $query->name =$data['name'];
            $query->description =$data['description'] ?? null;
            $query->status =$data['status'] ?? 'active';
            $query->updated_by =auth('api')->id() ?? Auth::id();
            
            $query->save();
            DB::commit();
            $this->log_user_activity('Inventory Classification Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Classification Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    --------------------------------------------------------
    Inventory Settings Item Types Functions
    --------------------------------------------------------
    */
    public function inventory_settings_item_type_create($data){
        DB::beginTransaction();

        try{
            $brand = ItemType::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            DB::commit();
            $this->log_user_activity('Inventory Item Type Create', $brand->id, true); 
            return $brand;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Item Type Create', null, false);
            return $e->getMessage();    
        }
    }

    public function inventory_settings_item_type_deactivate($id){
        DB::beginTransaction();

        try{
            $brand = ItemType::find($id);
            
            $brand->status = $brand->status == 1 ? 0 : 1;
            $brand->updated_by = auth('api')->id() ?? Auth::id();

            $brand->save();
            
            DB::commit();
            $this->log_user_activity('Inventory Item Type Deactivated', $id, true);
            return $brand;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Item Type Deactivated', $id, false);
            return $e->getMessage();
        }
    }

    public function inventory_settings_item_type_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'active':
                $query = ItemType::where('status', '=', 1);
            break;
            case 'inactive':
                $query = ItemType::where('status', '=', 0);
            break;
            default:
                $query = ItemType::query();
        }

        $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
        $query->orderBy('name', 'ASC');
        $query = $paginated? $query->paginate(20) : $query->get();
        
        return $query;
    }

    public function inventory_settings_item_type_get_by($type, $id, $detailed){
       $query = ItemType::where('id', '=', $id);
       $query = $detailed ? $query->with(['creator', 'updater', 'deleter']) : $query->select('id', 'name');
       $query->first();

       return $query;
    }

    public function inventory_settings_item_type_update($data, $id){
        DB::beginTransaction();

        try{
            $query = ItemType::findOrFail($id);

            $query->name =$data['name'];
            $query->description =$data['description'] ?? null;
            $query->status =$data['status'] ?? 1;
            $query->updated_by =auth('api')->id() ?? Auth::id();
            
            $query->save();
            DB::commit();
            $this->log_user_activity('Inventory Item Type Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Item Type Update', $id, false);
            return $e->getMessage();
        }
    }

}