<?php
namespace App\Http\Traits\Procurement;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Procurement\WorkOrder;
use App\Models\Procurement\WorkOrderItem;
use App\Models\Procurement\Vendor;
use App\Models\Procurement\VendorCategory;
use App\Models\Procurement\VendorContactPerson;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;


trait WorkOrderTrait {
    use FileManagerTrait, LogTrait;

    public function procurement_work_order_assign_vendor($data){
        DB::beginTransaction();

        try{
            $procurement = WorkOrder::find($data['wo_id']);
            
            $procurement->vendor_id = $data['vendor_id'];
            $procurement->updated_by = auth('api')->id() ?? Auth::id();

            $procurement->save();
            
            $this->log_user_activity('Procurement Work Order Vendor Assignment', $data['wo_id'], true); 
            DB::commit();
            return $procurement;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Work Order Vendor Assignment', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_work_order_create($data){
        DB::beginTransaction();

        try{
            $work_order = WorkOrder::create([
                'name' => $data['name'],
                'unique_id' => $data['unique_id'] ?? $this->procurement_work_order_unique_id(),
                'department_id' => $data['department_id'],
                'vendor_id' => $data['vendor_id'] ?? null,
                'type_id' => $data['type_id'] ?? 'LPO',
                'payment_term_id' => $data['payment_term_id'],
                'delivery_date' => $data['delivery_date'] ?? NULL,
                'date' => $data['date'] ?? date('Y-m-d'),
                'additional_cost' => $data['additional_cost'] ?? null,
                'taxes' => $data['taxes'] ?? null,
                'logistics' => $data['logistics'] ?? null,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            foreach ($data['items'] as $item){
                WorkOrderItem::create([
                    'wo_id' => $work_order->id,
                    'item' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'total_price' => $item['total_price'] ?? (!is_null($item['unit_price'])) ? ($item['unit_price'] * ($item['approved_quantity'] ?? $item['quantity'] ?? 0))  :  0,
                    'status' => 1,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }
            $this->log_user_activity('Procurement Work Order Create', $work_order->id, true); 
            DB::commit();
            return $work_order;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Work Order Create', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_work_order_delete($id){
        DB::beginTransaction();

        try{
            $query = WorkOrder::find($id);
            $query->status = 0;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();

            WorkOrderItem::where('po_id', '=', $query)->delete();
            
            $this->log_user_activity('Procurement Work Order Delete', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Work Order Delete', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_work_order_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = WorkOrder::withTrashed();
            break;
            case 'mine':
                $query = WorkOrder::where('created_by', '=', Auth::id() ?? auth('api')->id());
            break;
            case 'search':
                $vendors = Vendor::where('name', 'LIKE', "%$specific%")->pluck('id');
                $query = WorkOrder::where('unique_id', 'LIKE', "%$specific%")->orWhereIn('vendor_id', $vendors )->withTrashed();
            break;
            case 'status':
                if ($specific == 'all'){
                    $drafts = WorkOrder::where('status', '=', 0)->where('created_by', '=', Auth::id() ?? auth('api')->id());
                    $query = WorkOrder::where('status', '!=', 0)->where('status', '<', 10);
                    $query = $query->union($drafts);
                }
                else{
                    $query = WorkOrder::where('status', '=', $specific);
                }
            break;
            
        }
        $query = $query->orderBy('name', 'ASC');
        $query = $detailed ? $query->with(['department', 'order_items', 'payment_term', 'vendor']) : $query->select('id', 'name', 'unique_id');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function procurement_work_order_get_by($type, $specific, $detailed){
        switch($type){
            case 'id':
                $query = WorkOrder::where('id', '=', $specific);
            break;
            case 'unique_id':
                $query = WorkOrder::where('unique_id', '=', $specific);
            break;
        }

        $query = $detailed ? $query->with(['order_items', 'creator', 'deleter', 'payment_term', 'department', 'branch', 'updater', 'vendor']) : $query->select('id', 'name', 'unique_id');
        return $query->first();
    }

    public function procurement_work_order_unique_id(){
        return time();
    }
    
    public function procurement_work_order_update($data, $id){
        DB::beginTransaction();
        try{
            $active_items = [];
            $work_order = WorkOrder::find($id);
            
            $work_order->additional_cost    = $data['additional_cost'] ?? $work_order->additional_cost;
            $work_order->date               = $data['date'] ?? $work_order->date;
            $work_order->department_id      = $data['department_id'] ?? $work_order->department_id;
            $work_order->delivery_date      = $data['delivery_date'] ?? $work_order->delivery_date;
            $work_order->description        = $data['description'] ?? $work_order->description;
            $work_order->discount           = $data['discount'] ?? $work_order->discount;
            $work_order->logistics          = $data['logistics'] ?? $work_order->logistics;
            $work_order->payment_term_id    = $data['payment_term_id'] ?? $work_order->payment_term_id;
            $work_order->status             = $data['status'] ?? $work_order->status;
            $work_order->taxes              = $data['taxes'] ?? $work_order->taxes;
            $work_order->type_id            = $data['type_id'] ?? $work_order->type_id;;
            $work_order->updated_by         = auth('api')->id() ?? Auth::id();
            $work_order->vendor_id          = $data['vendor_id'] ?? $work_order->vendor_id;
            
            $work_order->save();

            if (isset($data['items']) && is_array($data['items'])){
                foreach ($data['items'] as $item){
                    if (isset($item['id'])){
                        $work_order_item = WorkOrderItem::find($item['id']);

                        $work_order_item->item = $item['name'];
                        $work_order_item->quantity = $item['quantity'];
                        $work_order_item->unit_price = $item['unit_price'];
                        $work_order_item->total_price = (!is_null($item['unit_price'])) ? ($item['unit_price'] * $item['quantity'])  :  0.00;
                        $work_order_item->status = 1;
                        $work_order_item->updated_by = auth('api')->id() ?? Auth::id();
                        
                        $work_order_item->save();
                    }
                    else{
                        WorkOrderItem::create([
                            'wo_id' => $work_order->id,
                            'item' => $item['name'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'] ?? 0,
                            'total_price' => $item['total_price'] ?? (!is_null($item['unit_price'])) ? ($item['unit_price'] * ($item['quantity'] ?? 0))  :  0,
                            'status' => 1,
                            'created_by' => auth('api')->id() ?? Auth::id(),
                            'updated_by' => auth('api')->id() ?? Auth::id(),
                        ]);
                    }
                    array_push($active_items, $item['item_id']);
                }

                WorkOrderItem::where('po_id', '=', $work_order)->whereNotIn('item_id', $active_items)->delete();
            }

            $this->log_user_activity('Procurement Work Order Update', $id, true);
            DB::commit();
            return $work_order;
        }   
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Procurement Work Order Update', $id, false);
            return $e->getMessage();
        } 
        
    }

    /*
    ---------------------------------------------------------------------------------------------------------
    Work Order Item Basic Functions 
    ---------------------------------------------------------------------------------------------------------
    */
    public function procurement_work_order_item_create($data){
        WorkOrderItem::create([
            'wo_id' => $data['wo_id'],
            'item' => $data['item'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'] ?? 0,
            'total_price' => $data['total_price'] ?? (!is_null($data['unit_price'])) ? ($data['unit_price'] * $data['quantity'])  :  0,
            'status' => 1,
            'created_by' => auth('api')->id() ?? Auth::id(),
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);
    }

    public function procurement_work_order_item_delete($id){
        $query = WorkOrderItem::find($id);

        $query->status = 0;
        $query->updated_by = auth('api')->id() ?? Auth::id();
        $query->deleted_by = auth('api')->id() ?? Auth::id();
        $query->deleted_at = date('Y-m-d H:i:s');
        
        $query->save();
    }

    public function procurement_work_order_item_update($data, $id){
        $query = WorkOrderItem::find($id);

        $query->wareo_id = $data['wo_id'];
        $query->item = $data['item'];
        $query->quantity = $data['quantity'];
        $query->unit_price = $data['unit_price'] ?? 0;
        $query->total_price = (!is_null($data['unit_price'])) ? ($data['unit_price'] * ($data['approved_quantity'] ?? $data['quantity'] ?? 0))  :  0;
        $query->status = 1;
        $query->updated_by = auth('api')->id() ?? Auth::id();
        
        $query->save();
    }
}