<?php

namespace App\Http\Traits\Operations;
use App\Http\Traits\General\LogTrait;

use App\Models\Operations\Branch;
use App\Models\EMR\Service;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Insurance\Plan;
use App\Models\Insurance\ProviderType;
use App\Models\Inventory\Item;
use App\Models\Operations\BranchPlanPriceList;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\Exception;
use Exception;
use Illuminate\Support\Facades\DB;


trait PriceListTrait{
    use LogTrait;

    public function operation_branch_plan_price_list_get_all($type, $specific, $detailed, $paginated){
        $query = BranchPlanPriceList::query();
        switch ($type){
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0);
            break;
        }

        if (is_array($specific)){
            if (!empty($specific['branch_id'])){
                $query = $query->where('branch_id', '=', $specific['branch_id']);
            }
            if (!empty($specific['plan_id'])){
                $query = $query->where('plan_id', '=', $specific['plan_id']);
            }
            if(!empty($specific['price_list_id'])){
                $query = $query->where('price_list_id', '=', $specific['price_list_id']);
            }
            if(!empty($specific['query'])){
                $query = $query->whereHas('price_list', function($q) use ($specific){
                    $q->where('name', 'LIKE', '%'.$specific['query'].'%');
                });
            }
        }
        //$query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['branch', 'plan', 'price_list']) : $query->select('id', 'name', 'unique_id', 'status');
        $price_lists = $paginated ? $query->paginate(20) : $query->get(); 
        
        return $price_lists;
    }
    public function operation_price_list_create($data){
        DB::beginTransaction();
        try{     
            $price_list = PriceList::create([
                'name' => $data['name'],
                'unique_id' => $data['unique_id'],
                'status' => $data['status'] ?? NULL,
                'description' => $data['description'] ?? NULL,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);    
            $this->log_user_activity( 'Operation Price List Create', true, $price_list->id);
            DB::commit();
            return $price_list;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Price List Create', false, null);
        } 
    }

    public function operation_price_list_delete($id){
        DB::beginTransaction();
        try{
            $price_list = PriceList::find($id);

            $price_list->status = $price_list->status == 0 ? 1 : 0;
            $price_list->updated_by = auth('api')->id() ?? Auth::id();

            $price_list->save();

            //Find branches using this price list
            $branches = Branch::where('price_list_id', '=', $id)->get();

            foreach ($branches as $branch){
                $branch->price_list_id = null;
                $branch->updated_by = auth('api')->id() ?? Auth::id();
                $branch->updated_at = date('Y-m-d H:i:s');

                $branch->save();
            }

            $this->log_user_activity( 'Operation Price List Delete', true, $price_list->id);
            DB::commit();
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Price List Delete', false, null);
        }

    }

    public function operation_price_list_get_all($type, $spec_id, $detailed, $paginated, $page){
        $query = PriceList::query();
        switch ($type){
            case 'active':
                $query = PriceList::where('status', '=', 1);
            break;
            case 'branch':
                $query = PriceList::where('branch_id', '=', $spec_id);
            case 'inactive':
                $query = PriceList::where('status', '=', 0);
            break;
            default:
            break;
        }
        $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query->select('id', 'name', 'unique_id', 'status');
        $price_lists = $paginated ? $query->paginate(20) : $query->get(); 
        
        return $price_lists;
    }

    public function operation_price_list_get_by($type, $id, $detailed){
        switch ($type){
            case 'unique_id':
                $query = PriceList::where('unique_id', '=', $id);
            break;
            case 'id':
                $query = PriceList::where('id', '=', $id);
            break;
            default:
                $query = PriceList::where('id', '=', $id)->orWhere('unique_id', '=', $id);
            break;
        }

        $query = $detailed ? $query->with(['creator', 'deleter', 'updater']) : $query;
        
        return $query->first();
    }

    public function operation_price_list_get_price_list_by_id($id, $detailed){
        $query = PriceList::where('id', '=', $id);
        $query = $detailed ? $query->with(['branch', 'plan.provider']) : $query->select('id', 'name', 'branch_id');
        $price_list = $query->first();

        return $price_list;
    }

    public function operation_price_list_update($data, $id){
        DB::beginTransaction();
        try{ 
            $price_list = PriceList::where('id', '=', $id)->first();

            $price_list->name = $data['name'];
            $price_list->unique_id = $data['unique_id'];
            $price_list->description = $data['description'] ?? NULL;
            $price_list->status = $data['status'];
            $price_list->updated_by = auth('api')->id();
    
            $price_list->save();
       
            $this->log_user_activity( 'Operation Price List Update', true, $price_list->id);
            DB::commit();

            return $price_list;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Price List Update', false, null);
        } 

    }

    //Price List Items Traits
    public function operation_price_list_item_create($data, $id){
        DB::beginTransaction();
        try{
            $price_list_item = PriceListItem::updateOrcreate([
                    'item_id' => $data['item_id'],
                    'price_list_id' => $data['price_list_id'] ?? $id,
                ], 
                [
                    'price' => $data['price'],
                    'covered' => $data['covered'] == 'yes' ? 1 : 0,
                    'coverage' => $data['coverage'] ?? 0.00,
                    'requires_code' => $data['requires_code'] == 'yes' ? 1 : 0,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]
            );
            $this->log_user_activity( 'Operation Price List Create', true, $price_list_item->id);
            DB::commit();

            return $price_list_item;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Price List Create', false, null);
        }
    }

    public function operation_price_list_item_default(){
        $query = Item::select(
            DB::raw('`emr_service_types`.`name` as `service_name`'), 
            DB::raw('`inventory_items`.`name`'), 
            DB::raw('0.00 as `price`'),
            DB::raw('"no" as `covered`'),
            DB::raw('0.00 as `coverage`'),
            DB::raw('"no" as `requires_code`'),
            DB::raw('"" as `pricing_type`'),
            DB::raw('"" as `maximum_sessions`'),
            DB::raw('"" as `maximum_session_cost`'),
        )
        
        ->leftJoin('emr_service_types', 'inventory_items.service_type_id', '=', 'emr_service_types.id')
        ->orderBy(DB::raw('`inventory_items`.`name`'), 'ASC')
        ->get();

        return $query;
    }

    public function operation_price_list_item_get_by_price_list_id($price_list_id, $items, $detailed){
        $query = Item::select(
                DB::raw('`inventory_items`.`id` as `item_id`'),
                DB::raw('`inventory_items`.`name`'), 
                //DB::raw('`inventory_items`.`service_type_id`'), 
                //DB::raw('`emr_service_types`.`name` as `service_name`'), 
                //DB::raw('`finance_price_list_items`.`price_list_id`'),
                DB::raw('`finance_price_list_items`.`price`'),
                DB::raw('`finance_price_list_items`.`covered`  as `covered` '),
                DB::raw('`finance_price_list_items`.`coverage`'),
                DB::raw('`finance_price_list_items`.`requires_code` as `requires_code`'),
            )
            ->leftJoin('finance_price_list_items', function($join) use ($price_list_id){
                $join->on('inventory_items.id', '=', 'finance_price_list_items.item_id');
                $join->on('finance_price_list_items.price_list_id','=', DB::raw($price_list_id));
            })
        ->orderBy(DB::raw('`inventory_items`.`name`'), 'ASC')
        ->get();

        return $query ;
    }

    public function operation_price_list_item_search_by_query($question, $price_list_id){
        
        $query = Item::orderBy('name', 'ASC');

        $brand_id = $question['brand_id']; 
        $category_id = $question['category_id']; 
        $item_name = $question['brand_id']; 
        $service_type_id = $question['service_type_id']; 
        
        $query = Item::select([
            'inventory_items.id as item_id',
            'inventory_items.name',
            DB::raw('COALESCE(finance_price_list_items.price, 0) as price'),
            DB::raw('COALESCE(finance_price_list_items.covered, 0) as covered'),
            DB::raw('COALESCE(finance_price_list_items.coverage, 0) as coverage'),
            DB::raw('finance_price_list_items.requires_code'),
        ])
        ->leftJoin('finance_price_list_items', function ($join) use ($price_list_id) {
            $join->on('inventory_items.id', '=', 'finance_price_list_items.item_id')
                ->where('finance_price_list_items.price_list_id', '=', $price_list_id);
        })
        ->orderBy('inventory_items.name', 'ASC');

    // Apply filters
        if (!empty($question['name'])) {
            $query->where('inventory_items.name', 'LIKE', '%' . $question['name'] . '%');
        }

        if (!empty($question['item_type_id'])) {
            $query->where('inventory_items.item_type_id', $question['item_type_id']);
        }

        if (!empty($question['category_id'])) {
            $query->where('inventory_items.category_id', $question['category_id']);
        }

        if (!empty($question['brand_id'])) {
            $query->where('inventory_items.brand_id', $brand_id);
        }
    
        return $query->get();
    }

    public function operation_price_list_item_update($data, $id){
        DB::beginTransaction();

        try{
            $price_list_item = PriceListItem::updateOrcreate([
                    'item_id' => $data['item_id'],
                    'price_list_id' => $data['price_list_id'] ?? $id,
                ], 
                [
                    'price' => $data['price'],
                    'covered' => $data['covered'] == 'yes' ? 1 : 0,
                    'coverage' => $data['coverage'] ?? 0.00,
                    'requires_code' => $data['requires_code'] == 'yes' ? 1 : 0,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]
            );

            DB::commit();
            $this->log_user_activity( 'Operation Price List Item Update', true, $id);
            return $price_list_item;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Price List Item Update', false, $id);
            return $e->getMessage();
        }
    }

    public function operation_price_list_item_update_by_price_list_id($data, $price_list_id){
        DB::beginTransaction();
        try{
            $price_list_item = PriceListItem::updateOrcreate([
                'item_id' => $data['item_id'],
                'price_list_id' => $price_list_id,
            ], 
            [
                'price' => $data['price'],
                'covered' => $data['covered'] == 'yes' ? 1 : 0,
                'coverage' => $data['coverage'] ?? 0.00,
                'requires_code' => $data['requires_code'] == 'yes' ? 1 : 0,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
            DB::commit();
            $this->log_user_activity( 'Operation Price List Item Update', true, $price_list_item->id);
            return $price_list_item;
        }
        catch (Exception $e){
            DB::rollback();
            $this->log_user_activity( 'Operation Price List Item Update', false, null);
        }
    }
}