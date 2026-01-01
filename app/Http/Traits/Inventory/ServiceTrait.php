<?php
namespace App\Http\Traits\Inventory;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Inventory\Category;
use App\Models\Inventory\Item;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
trait ServiceTrait {
    use FileManagerTrait, LogTrait;

    public function inventory_service_category_create_new($data){
        DB::beginTransaction();

        try{
            $query = Category::create([
                'name' => $data['name'], 
                'description' => $data['description'], 
                'type_id' => $data['type_id'],
                'primary_category_id' => $data['primary_category_id'], 
                'status' => $data['status'] ?? 1, 
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            $this->log_user_activity('Service Category Create', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Service Category Create', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_service_category_delete($id){
        DB::beginTransaction();

        try{
            $query = Category::find($id);
            
            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $this->log_user_activity('Service Category Delete', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Service Category Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function inventory_service_category_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = Category::where('status', '=', 1);
            break;
            case 'all':
                $query = Category::withTrashed();
            break;
            case 'deactivated':
                $query = Category::where('status', '=', 0)->withTrashed();
            break;
        }

        $query = $detailed ? $query->with(['category', 'creater', 'deleter', 'item_type', 'items', 'sub_categories', 'updater']) : $query->select('id', 'name');

        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query;
    }

    public function inventory_service_category_get_by($type, $specific, $detailed){
        switch($type){
            case 'id':
                $query = Category::where('id', '=', $specific);
            break;
        }

        $query = $detailed ? $query->with( ['category', 'creater', 'deleter', 'item_type', 'items', 'sub_categories', 'updater']) : $query->select('id', 'name');

        return $query->first();
    }

    public function inventory_service_category_update($data, $id){
        DB::beginTransaction();

        try{
            $query = Category::find($id);
        
            $query->name = $data['name']; 
            $query->description = $data['description']; 
            $query->type_id = $data['type_id'];
            $query->primary_category_id = $data['primary_category_id']; 
            $query->status = $data['status'] ?? 1;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            $query->save();

            $this->log_user_activity('Service Category Update', $query->id, true);
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Service Category Update', $id, false);
            return $e->getMessage();
        }
    }

}