<?php
namespace App\Http\Traits\Inventory;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Patient\Package as PatientPackage;
use App\Models\EMR\Patient\PackageItem as PatientPackageItem;
use App\Models\EMR\Visit;
use App\Models\Inventory\Category;
use App\Models\Inventory\Classification;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemType;
use App\Models\Inventory\PackageItem as InventoryPackageItem;
use App\Models\Inventory\Package as InventoryPackage;
use App\Models\Inventory\Store;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Inventory\StoreItemSetting;
use App\Models\Inventory\StoreUser;
use App\Models\Inventory\TransferOrder;
use App\Models\Inventory\TransferOrderItem;
use App\Models\Operations\Branch;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
trait TransferOrderTrait {
    use FileManagerTrait, LogTrait;

    public function inventory_sales_order_create($data){
        DB::beginTransaction();

        try{
            if ($data['patient_type_id'] == 'new_visit'){
                $branch = Branch::find(request()->cookie('current_branch'));
                $visit_data = [
                    'branch_id' => request()->cookie('current_branch'),
                    'patient_id' => $data['patient_id'],
                    'care_id' => $branch->price_list_id,
                    'start_date' => date('Y-m-d'),
                    'start_timestamp' => date('Y-m-d H:i:s'),
                    'end_date' => null,
                    'end_timestamp' => null,
                    'visit_type_id' => 4,
                    'status' => 1
                ];
                $visit = $this->emr_visit_create($visit_data);
                if (is_string($visit)){
                    DB::rollback();
                    $this->log_user_activity('Inventory Sales Order Request Create', null, false);
                    return $visit;
                }
            }
            else if($data['patient_type_id'] == 'active_visit'){
                $visit = $this->emr_visit_get_by('id', $data['visit_id'], false);
            }
            else{ $visit = null;}

            $query = TransferOrder::create([
                'issuing_store_id' => $data['issuing_store_id'],
                'name' => 'Point of Sale',
                'patient_id' => ($visit === null) ? 0 : $visit->id,
                'reference_type_id' => 2,
                'status' => $data['status'] ?? 1,
                'unique_id' => $data['unique_id'] ?? $this->inventory_transfer_order_unique_id_create(),
                'visit_id' => ($visit === null) ? null : $visit->id,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            if(isset($data['items']) && is_array($data['items'])){
                foreach ($data['items'] as $item){
                    TransferOrderItem::create([
                        'transfer_request_id' => $query->id,
                        'item_id' => $item['item_id'],
                        'requested_quantity' => $item['requested_quantity'],
                        'approved_quantity' => $item['approved_quantity'] ?? $item['requested_quantity'],
                        'transfer_quantity' => 0,
                    ]);

                    $this->finance_transaction_create($item['item_id'], (is_null($visit) ? 0 : $visit->patient_id), $item['requested_quantity'], false, (is_null($visit) ? null : $visit->id));
                }
            }

            DB::commit();
            $this->log_user_activity('Inventory Transfer Order Request Create', $query->id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Sales Order Request Create', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_sales_order_delete($id){
        
    }


    public function inventory_sales_order_get_all($type, $specific, $detailed, $paginated, $page){
        $user_stores = StoreUser::where('user_id', '=', Auth::id() ?? auth('api')->id())->pluck('store_id');
        $my_stores = Store::whereIn('id', $user_stores)->where('status', '=', 1)->orderBy('name', 'ASC')->get();

        $query = TransferOrder::where('reference_type_id', '=', 2);
        switch ($type){
            case 'all':
                $query = $query->where('status', '>', 0);  
            break;
            case 'out':
                $query = $query->whereIn('issuing_store_id', $user_stores);
            break;
            case 'search':
                if ($specific['t'] == 'all'){
                    $query = $query->where('status', '>', 0);
                }
                elseif ($specific['t'] == 'out'){
                    $query = $query->whereIn('issuing_store_id', $user_stores);
                }
                else{
                    $query = $query->where('status', '<=', 5);
                }

                if($specific['status'] != 'all'){
                    $query = $query->where('status', '=', $specific['status']);
                }
                $quest = $specific['query'];
                $query = $query->where('unique_id', 'LIKE', "%$quest%");
            break;
            case 'status':
                if ($specific['t'] == 'all'){
                    $query = $query = $query->where('status', '>', 0);
                }
                elseif ($specific['t'] == 'in'){
                    $query = $query->whereIn('requesting_store_id', $user_stores);
                }
                elseif ($specific['t'] == 'out'){
                    $query = $query->whereIn('issuing_store_id', $user_stores);
                }
                else{
                    $query = $query->where('status', '<=', 5);
                }

                $query->where('status', '=', $specific['status']);
            break;
        }

        $query = $detailed ? $query->with(['issuing_store', 'requesting_store']) : $query->select('id', 'name', 'unique_id');
        $query = $paginated ? $query->latest()->paginate(50) : $query->latest()->get();

        return $query;
    }

    public function inventory_sales_order_get_by($type, $specific, $detailed){
        
    }
    
    public function inventory_sales_order_update($data, $id){

    }

    private function inventory_transfer_order_unique_id_create(){
        return strtoupper(config('app.short_code').'-'.dechex(time()));
    }


    public function inventory_direct_issue($data){}


    public function inventory_transfer_order_cancel($data, $id){
        DB::beginTransaction();
        $user = Auth::user() ?? auth('api')->user();
        try{    
            $query = TransferOrder::find($id);

            $query->status = 10;
            $query->rejected_by = Auth::id() ?? auth('api')->id();
            $query->rejected_at = date('Y-m-d H:i:s'); 
            $query->rejection_note = "<p>".$user->first_name." ".$user->last_name." rejected. The following note was added:</p>".$data['note'];

            $query->save();

            DB::commit();
            $this->log_user_activity('Inventory Transfer Order Request Reject', $id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Transfer Order Request Reject', $id, false);
            return $e->getMessage();
        }
    }
    
    public function inventory_transfer_order_create($data){
        DB::beginTransaction();

        try{
            $query = TransferOrder::create([
                'name' => $data['name'] ?? null,
                'unique_id' => $data['unique_id'] ?? $this->inventory_transfer_order_unique_id_create(),
                'description' => $data['description'] ?? null,
                'reference_type_id' => 2,
                'requesting_store_id' => $data['requesting_store_id'],
                'issuing_store_id' => $data['issuing_store_id'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            if(isset($data['items']) && is_array($data['items'])){
                foreach ($data['items'] as $item){
                    TransferOrderItem::create([
                        'transfer_request_id' => $query->id,
                        'item_id' => $item['item_id'],
                        'requested_quantity' => $item['requested_quantity'],
                        'approved_quantity' => $item['approved_quantity'] ?? $item['requested_quantity'],
                        'transfer_quantity' => 0,
                    ]);
                }
            }
            DB::commit();
            $this->log_user_activity('Inventory Transfer Order Request Create', $query->id, true);
            
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Transfer Order Request Create', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_transfer_order_get_all($type, $specific, $detailed, $paginated, $page){
        $user_stores = StoreUser::where('user_id', '=', Auth::id() ?? auth('api')->id())->pluck('store_id');
        $my_stores = Store::whereIn('id', $user_stores)->where('status', '=', 1)->orderBy('name', 'ASC')->get();
        $query = TransferOrder::query();
        
        switch ($type){
                
            case 'all':
                $query = TransferOrder::where('status', '>', 0);  
            break;
            case 'in':
                $query = TransferOrder::whereIn('requesting_store_id', $user_stores);
            break;
            case 'out':
                $query = TransferOrder::whereIn('issuing_store_id', $user_stores);
            break;
            case 'search':
                if ($specific['t'] == 'all'){
                    $query = $query = TransferOrder::where('status', '>', 0);
                }
                elseif ($specific['t'] == 'in'){
                    $query = TransferOrder::whereIn('requesting_store_id', $user_stores);
                }
                elseif ($specific['t'] == 'out'){
                    $query = TransferOrder::whereIn('issuing_store_id', $user_stores);
                }
                else{
                    $query = TransferOrder::where('status', '<=', 5);
                }

                if($specific['status'] != 'all'){
                    $query = $query->where('status', '=', $specific['status']);
                }
                $quest = $specific['query'];
                $query = $query->where('unique_id', 'LIKE', "%$quest%");
            break;
            case 'status':
                if ($specific['t'] == 'all'){
                    $query = $query = TransferOrder::where('status', '>', 0);
                }
                elseif ($specific['t'] == 'in'){
                    $query = TransferOrder::whereIn('requesting_store_id', $user_stores);
                }
                elseif ($specific['t'] == 'out'){
                    $query = TransferOrder::whereIn('issuing_store_id', $user_stores);
                }
                else{
                    $query = TransferOrder::where('status', '<=', 5);
                }
                $query->where('status', '=', $specific['status']);
            break;
            case 'unapproved':
                $query = $query->where('approval_status', '=', TransferOrder::ApprovalStatusPending);
            break;
        }

        $query = $detailed ? $query->with(['issuing_store', 'requesting_store']) : $query->select('id', 'name', 'unique_id');
        $query = $paginated ? $query->latest()->paginate(50) : $query->latest()->get();

        return $query;
    }

    public function inventory_transfer_order_get_by($type, $specific, $detailed){
        //echo($type);
        $query = TransferOrder::where('id', '=', $specific)->orWhere('unique_id', '=', $specific);
        /*switch($type){
            case 'id':
                $query = TransferOrder::where('id', '=', $specific);
            break;
            case 'unique_id':
                $query = TransferOrder::where('unique_id', '=', $specific);
            break;
        }*/
        $query = $detailed ? $query->with(['accepter.department', 'approver.department', 'creator.department', 'issuing_store', 'items.item', 'rejecter', 'requesting_store' ]) : $query->select('id', 'unique_id', 'name');
        
        return $query->first();
    }

    public function inventory_transfer_order_summary_report($type, $start, $end, $period_type, $paginated){
        $query = TransferOrder::query()->whereBetween('created_at', [$start, $end]);

        switch($period_type){
            case 'day':
                $query_sql = "DATE(created_at)";
            break;
            case 'month':
                $query_sql = "DATE_FORMAT(created_at, '%Y-%m')";
            break;
            case 'week':
                $query_sql = "YEARWEEK(created_at)";       
            break;
        }

        switch($type){
            case 'approvals':
                $query = $query->select(
                    DB::raw($query_sql." as label"),
                        'approval_status', 
                        DB::raw('COUNT(*) as count')
                )->groupBy('label', 'approval_status')->get()->toArray();
                
            break;
        } 

        return $query;
    }
    public function inventory_transfer_order_update($data, $id){
        DB::beginTransaction();

        try{
            $query = TransferOrder::where('id', '=', $id)->first();
        
            $query->name = $data['name'] ?? null;
            $query->unique_id = $data['unique_id'] ?? $this->inventory_transfer_order_unique_id_create();
            $query->description = $data['description'] ?? null;
            $query->requesting_store_id = $data['requesting_store_id'];
            $query->issuing_store_id = $data['issuing_store_id'];
            $query->status = $data['status'];
            $query->updated_by = Auth::id() ?? auth('api')->id();

            if($data['status'] == 2){
                $query->approved_by = auth('api')->id() ?? Auth::id();
                $query->approved_at = date('Y-m-d H:i:s');
                $query->approval_note = $data['note'];
            }

            else if($data['status'] == 3){
                $query->accepted_by = auth('api')->id() ?? Auth::id();
                $query->accepted_at = date('Y-m-d H:i:s');
                $query->acceptance_note = $data['note'];
            }
            
            $query->save();
            if ($data['status'] < 4){
                $new_items = [];
                $transfer_order_items = TransferOrderItem::where('transfer_request_id', '=', $id)->get()->pluck('item_id')->toArray();
                
                foreach($data['items'] as $item){                    
                    $transfer_order_item = TransferOrderItem::where('transfer_request_id', '=', $query->id)->where('item_id', '=', $item['item_id'])->first();
                    if ($data['status'] <= 1){
                        $transfer_order_item->requested_quantity = $item['requested_quantity'];
                    }
                    else if($data['status'] == 2){
                        $transfer_order_item->approved_quantity = $item['approved_quantity'] ?? $item['requested_quantity'];
                    }
                    else if($data['status'] == 3){
                        $transfer_order_item->transfer_quantity = $item['transfer_quantity'] ?? $item['requested_quantity'];
                    }

                    $transfer_order_item->save();
                    array_push($new_items, $item['item_id']);
                }

                $item_to_delete = array_diff($transfer_order_items, $new_items);
                foreach($item_to_delete as $cast){
                    if (in_array($cast, $transfer_order_items)){
                        $transfer_order_item = TransferOrderItem::where('item_id', '=', $cast)->where('transfer_request_id', '=', $id)->first();
                        $transfer_order_item->deleted_at = date('Y-m-d H:i:s');
                        $transfer_order_item->save();
                    }
                }
            }
            DB::commit();
            $this->log_user_activity('Inventory Transfer Order Request Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Transfer Order Request Update', $id, false);
            return $e->getMessage();
        }
    }

    
}