<?php
namespace App\Http\Traits\Procurement;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Inventory\StoreItem;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Procurement\Batch;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrder as ProcurementPurchaseOrder;
use App\Models\Procurement\PurchaseOrderApproval;
use App\Models\Procurement\PurchaseOrderItem;
use App\Models\Procurement\Vendor;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


trait PurchaseOrderTrait {
    use FileManagerTrait, LogTrait;

    private function purchase_generateRandomString($length = 10){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    private function purchase_generate_unique_id($type){
        //return uniqid($type . '_');
        $code = $this->purchase_generateRandomString(10);
        switch($type){
            case 'purchase_order':
                $prefix = 'PO';   
                $query = PurchaseOrder::where('unique_id', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->purchase_generate_unique_id('order');
                }else{
                    return $prefix.'-'.$code;
                }
            case 'order_item':
                $prefix = 'POI';   
                $query = PurchaseOrderItem::where('uuid', '=', $prefix.'-'.$code)->first();
                if($query){
                    return $this->purchase_generate_unique_id('order_item');
                }else{
                    return $prefix.'-'.$code;
                }   
        }
    }

    private function procurement_unique_id($type){
        switch($type){
            case 'purchase_order':
                $prefix = 'PO-';
                $code = Str::uuid();
                $query = PurchaseOrder::where('unique_id', '=', $prefix.$code)->first();
                if($query){
                    return $this->generate_unique_id('purchase_order');
                }
                else{
                    return $prefix.$code;
                }
            case 'goods_received':
                $prefix = 'GRN-';
                $code = Str::uuid();
                $query = Batch::where('unique_id', '=', $prefix.$code)->first();
                if($query){
                    return $this->generate_unique_id('purchase_order');
                }
                else{
                    return $code;
                }
        }
        
    }
    
    public function procurement_batches_by($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'fulfillment':
                $query = PurchaseOrderItem::where('po_id', '=', $specific);
            break;
        }
        $query = $detailed ? $query->with(['batches', 'item', 'purchase_order', 'purchase_order_item']) : $query->select('id')->with(['batches']);
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query; 
    }

    public function procurement_batches_confirm_single($data, $id){
        DB::beginTransaction();
        try{
            $query = Batch::find($id);

            $query->status = ($data['decision'] == 'confirm') ? 1 : 0;
            $query->confirmed_by = Auth::id() ?? auth('api')->id();
            $query->confirmed_at = date('Y-m-d H:i:s');

            if($data['decision'] == 'confirm'){
                //update the PO item status and fulfilled quantity
                $po_item = PurchaseOrderItem::find($query->po_item_id);
                $confirmed_items_qty = Batch::where('po_item_id', '=', $query->po_item_id)->where('status', '=', 1)->sum('total_quantity'); 
                
                $po_item_qty = $po_item->total_quantity;
                
                $po_item->status = ($po_item_qty <= $confirmed_items_qty) ? 4 : 3;
                $po_item->updated_by = Auth::id() ?? auth('api')->id();

                $po_item->save();
                
                //update the PO status
                $purchase_order = PurchaseOrder::find($query->po_id);
                $pending = PurchaseOrderItem::where('po_id', '=', $query->po_id)->where('status', '=', 4)->count();
                
                $purchase_order->status = ($pending == 0) ? 6 : 4;
                $purchase_order->updated_by = auth('api')->id() ?? Auth::id();    
                
                $purchase_order->save();
                
                //Add to PO Store
                $store_item_batch = $this->inventory_store_items_increase_quantity($query->item_id, $purchase_order->store_id, $query->id, $query->quantity);
            }

            $query->save();
                
            $this->log_user_activity('Procurement Purchase Order GRN Confirmed', $id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order GRN Confirmed', $id, false);
            return $e->getMessage();
        }
    }

    public function procurement_batches_confirm($data, $id){
        DB::beginTransaction();

        try{
            $purchase_order = PurchaseOrder::find($id);
            
            foreach ($data['items'] as $order_item){
                foreach($order_item['batches'] as $batch){
                    $query = Batch::find($batch['id']);

                    $query->status = $batch['status'];
                    $query->confirmed_by = Auth::id() ?? auth('api')->id();
                    $query->confirmed_at = date('Y-m-d H:i:s');

                    $query->save();
                }
                //check if the total amount received is same as required by PO
                $po_item_qty = PurchaseOrderItem::find($order_item['po_item_id'])->pluck('total_quantity'); print($po_item_qty);
                $confirmed_items_qty = Batch::where('po_item_id', '=', $order_item['po_item_id'])->where('status', '=', 1)->sum('total_quantity'); echo ($confirmed_items_qty);
                
                
                $po_item = PurchaseOrderItem::find($order_item['po_item_id']);
                $po_item->status = ($po_item_qty <= $confirmed_items_qty) ? 4 : 3;
                $po_item->updated_by = Auth::id() ?? auth('api')->id();

                $po_item->save();
            }
            $pending = PurchaseOrderItem::where('po_id', '=', $id)->where('status'. '=', 4)->count();
            
            $purchase_order->status = ($pending == 0) ? 6 : 4;
            $purchase_order->updated_by = auth('api')->id() ?? Auth::id();    
            
            $purchase_order->save();
                
            $this->log_user_activity('Procurement Purchase Order GRN Confirmed', $id, true); 
            DB::commit();
            return $purchase_order;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order GRN Confirmed', $id, false);
            return $e->getMessage();
        }
    }

    public function procurement_batches_create($data){
        DB::beginTransaction();
        
        try{
            $query = PurchaseOrder::find($data['po_id']);
            if (($query->status < 2) || ($query->status == 10)){
                return "Invalid Purchase Order";
            } 
            else{
                $query->status = 3;
                $query->updated_by = Auth::id() ?? auth('api')->id();

                foreach ($data['items'] as $item){
                    foreach($item['batches'] as $batch){
                        if ($batch['quantity'] > 0){
                            Batch::create([
                                'unique_id' => $this->procurement_unique_id('goods_received'),
                                'item_id' => $item['item']['item_id'],
                                'po_item_id' => $item['item']['id'],
                                'po_id' => $data['po_id'],
                                'package_id' => $item['item']['package_id'],
                                'package_quantity' => $item['item']['package_quantity'],
                                'batch_number' => $batch['batch_number'],
                                'expiry_date' => $batch['expiry_date'],
                                'quantity' => $batch['quantity'],
                                'status' => Batch::StatusPending,
                                'total_quantity' => $batch['quantity'] * $item['item']['package_quantity'],
                                'created_by' => AUth::id() ?? auth('api')->id(),
                                'updated_by' => AUth::id() ?? auth('api')->id(),
                            ]);
                        }
                    }
                }
                $query->save();
            }

            $this->log_user_activity('Procurement Goods Received Note Created', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Goods Received Note Created', null, false);
            return $e->getMessage();
        }
    } 

    public function procurement_batches_delete($id){
        DB::beginTransaction();
        
        try{
            $query = Batch::find($id);

            if ($query->status == Batch::StatusDeleted){
                $query->status = Batch::StatusPending;
                $query->deleted_by = null;
                $query->deleted_at = null;    
            }
            else{
                $query->status = Batch::StatusDeleted;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            $this->log_user_activity('Procurement Goods Received Note Deleted', $id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Goods Received Note Deleted', $id, false);
            return $e->getMessage();
        }
    } 

    public function procurement_batches_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Batch::query();
        switch ($type){
            case 'purchase_order_item':
                $query = $query->where('po_item_id', '=', $specific);
            break;
            case 'purchase_order':
                $query = $query->where('po_id', '=', $specific);
            break;
            case 'unapproved':
                $query = $query->where('status', '=', Batch::StatusPending);
            break;
            
        }
        $query = $detailed ? $query->with(['approver', 'creator', 'deleter', 'item', 'purchase_order', 'purchase_order_item', 'updater']) : $query->select('id')->with(['batches']);
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query;
    }

    public function procurement_batches_get_by($type, $id, $detailed){
        try{
            $query = Batch::where('id', '=', $id);
            $query = $detailed ? $query->with(['approval', 'creator', 'purchase_order', ]) : $query->select('id', 'batch_number', 'unique_id');
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function procurement_purchase_order_additional_cost($data, $id){
        DB::beginTransaction();

        try{
            $procurement = PurchaseOrder::find($id);
            
            $procurement->taxes = $data['taxes'];
            $procurement->logistics = $data['logistics'];
            $procurement->discount = $data['discount'];
            $procurement->updated_by = auth('api')->id() ?? Auth::id();

            $procurement->save();
            
            $this->log_user_activity('Procurement Purchase Order Additional Cost Modified', $id, true); 
            DB::commit();
            return $procurement;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order Additional Cost Modified', $id, false);
            return $e->getMessage();
        }
    }

    public function procurement_purchase_order_approve($data, $id){
        DB::beginTransaction();

        try{
            $procurement = PurchaseOrder::findOrFail($id);
            
            PurchaseOrderApproval::create([
                'po_id' => $id,
                'approved_by' => Auth::id() ?? auth('api')->id(),
                'remark' => $data['remark'],
                'decision' => $data['decision'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            $procurement->status = $data['decision'] == 'confirm' ? PurchaseOrder::StatusApproved : PurchaseOrder::StatusRejected;
            $procurement->updated_by = auth('api')->id() ?? Auth::id();

            $procurement->save();
            
            $this->log_user_activity('Procurement Purchase Order Approved', $id, true); 
            DB::commit();
            return $procurement;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order Approved', $id, false);
            return $e->getMessage();
        }
    }

    public function procurement_purchase_order_assign($type, $data){
        DB::beginTransaction();

        try{
            $procurement = PurchaseOrder::where('unique_id', '=', $data['po_id'])->orWhere('id', '=', $data['po_id'])->first();

            switch ($type){
                case 'store':
                    $procurement->store_id = $data['store_id'];
                break;
                case 'vendor':
                    $procurement->vendor_id = $data['vendor_id'];
                break;
            }
            
            $procurement->updated_by = auth('api')->id() ?? Auth::id();

            $procurement->save();
            
            $this->log_user_activity('Procurement Purchase Order Assignment', $procurement->id, true); 
            DB::commit();
            return $procurement;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order Store Assignment', null, false);
            return $e->getMessage();
        }
    }
    public function procurement_purchase_order_assign_vendor($data){
        DB::beginTransaction();

        try{
            $procurement = PurchaseOrder::find($data['po_id']);
            
            $procurement->vendor_id = $data['vendor_id'];
            $procurement->updated_by = auth('api')->id() ?? Auth::id();

            $procurement->save();
            
            $this->log_user_activity('Procurement Purchase Order Vendor Assignment', $data['po_id'], true); 
            DB::commit();
            return $procurement;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order Vendor Assignment', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_purchase_order_create($data){
        DB::beginTransaction();

        try{
            $purchase_order = PurchaseOrder::create([
                'name' => $data['name'],
                'unique_id' => $data['unique_id'] ?? $this->purchase_generate_unique_id('purchase_order'),
                'store_id' => $data['store_id'],
                'vendor_id' => $data['vendor_id'],
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

            foreach ($data['order_items'] as $item){
                PurchaseOrderItem::create([
                    'po_id' => $purchase_order->unique_id,
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'approved_quantity' => ($item['approved_quantity'] ?? $item['quantity'] ?? 0),
                    'package_id' => $item['package_id'] ?? null,
                    'package_quantity' => $item['package_quantity'] ?? 1,
                    'total_quantity' => $item['total_quantity'] ?? (($item['approved_quantity'] ?? $item['quantity'] ?? 0) * ( $item['package_quantity'] ?? 1)),
                    'unit_price' => $item['unit_price'] ?? 0,
                    'total_price' => $item['total_price'] ?? (!is_null($item['unit_price'])) ? ($item['unit_price'] * ($item['approved_quantity'] ?? $item['quantity'] ?? 0))  :  0,
                    'status' => 1,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }


            $this->log_user_activity('Procurement Purchase Order Create', $purchase_order->id, true); 
            DB::commit();
            return $purchase_order;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order Create', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_purchase_order_delete($id){
        DB::beginTransaction();

        try{
            $query = PurchaseOrder::findOrFail($id);
            if ($query->status == PurchaseOrder::StatusDraft || $query->status == PurchaseOrder::StatusRejected){
                $query->status = PurchaseOrder::StatusDeleted;
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_at = date('Y-m-d H:i:s');
                PurchaseOrderItem::where('po_id', '=', $id)->update([
                    'deleted_by' => auth('api')->id() ?? Auth::id(),
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);
            }
            else{
                $query->status = PurchaseOrder::StatusRejected;
                $query->updated_by = auth('api')->id() ?? Auth::id();
            }

            $query->save();

            $this->log_user_activity('Procurement Purchase Order Delete', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order Delete', null, false);
            return $e->getMessage();
        }
    }

    public function procurement_purchase_order_get_all($type, $specific, $detailed, $paginated, $page){
        $query = PurchaseOrder::query();

        switch($type){
            case 'mine':
                $query = $query->where('created_by', '=', Auth::id() ?? auth('api')->id());
            break;
        }
        
        if(isset($specific['status']) && !empty($specific['status'])){
            switch($specific['status']){
                case 'all':
                    $query = $query->withTrashed();
                break;
                case 'active':
                    $query = $query->where('status', '!=', PurchaseOrder::StatusDeleted);
                break;
                case 'approved':
                    $query = $query->where('status', '=', PurchaseOrder::StatusApproved);
                break;
                case 'completed':
                $query = $query->where('status', '=', PurchaseOrder::StatusCompleted);
                break;
                case 'draft':
                    $query = $query->where('status', '=', PurchaseOrder::StatusDraft);
                break;
                case 'ongoing':
                    $query = $query->where('status', '>=', PurchaseOrder::StatusApproved)->where('status', '<', PurchaseOrder::StatusCompleted);
                break;
                case 'pending':
                    $query = $query->where('status', '=', PurchaseOrder::StatusAwaitingApproval);
                break;
                case 'rejected':
                    $query = $query->where('status', '=', PurchaseOrder::StatusRejected);
                break;
            }
        }

        if (isset($specific['query']) && !empty($specific['query'])){
            $search = $specific['query']; 
            $vendors = Vendor::where('name', 'LIKE', "%$search%")->pluck('id');
            $query = $query->where('unique_id', 'LIKE', "%$search%")->orWhereIn('vendor_id', $vendors )->withTrashed();
        }
        $query = $query->latest();
        $query = $detailed ? $query->with(['store', 'vendor', 'payment_term']) : $query->select('id', 'name', 'unique_id');
        $query = $paginated ? $query->paginate(50) : $query->get();
        
        return $query;
    }

    public function procurement_purchase_order_get_by($type, $specific, $detailed){
        switch($type){
            case 'id':
                $query = PurchaseOrder::where('id', '=', $specific);
            break;
            case 'unique_id':
                $query = PurchaseOrder::where('unique_id', '=', $specific);
            break;
        }

        $query = PurchaseOrder::where('unique_id', '=', $specific)->orWhere('id', '=', $specific);
        $query = $detailed ? $query->with(['approvals.approver', 'batches.item', 'order_items.item', 'order_items.package', 'creator', 'deleter', 'payment_term', 'store.branch', 'updater', 'vendor']) : $query->select('id', 'name', 'unique_id');
        return $query->withTotal()->first();
    }

    public function procurement_purchase_order_initiate(){
        DB::beginTransaction();

        try{
            $purchase_order = PurchaseOrder::create([
                'unique_id' => $this->procurement_unique_id('purchase_order'), 
                'status' => 0, 
                'created_by' => auth('api')->id() ?? Auth::id(), 
                'updated_by' => auth('api')->id() ?? Auth::id(), 
            ]);
            DB::commit();
            $this->log_user_activity('Procurement Purchase Order Create', $purchase_order->id, true); 
            return $purchase_order;
            
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Procurement Purchase Order Create', null, false); 
            return $e->getMessage();
        }
    }
    
    public function procurement_purchase_order_update($data, $id){
        DB::beginTransaction();
        try{
            $active_items = [];
            $purchase_order = PurchaseOrder::find($id);
            
            $purchase_order->store_id = $data['store_id'];
            $purchase_order->vendor_id = $data['vendor_id'];
            $purchase_order->type_id = $data['type_id'];
            $purchase_order->payment_term_id = $data['payment_term_id'] ?? $purchase_order->payment_term_id;
            $purchase_order->delivery_date = $data['delivery_date'] ?? $purchase_order->delivery_date;
            $purchase_order->date = $data['date'] ?? $purchase_order->date;
            $purchase_order->additional_cost = $data['additional_cost'] ?? $purchase_order->additional_cost;
            $purchase_order->taxes = $data['taxes'] ?? $purchase_order->taxes;
            $purchase_order->logistics = $data['logistics'] ?? $purchase_order->logistics;
            $purchase_order->description = $data['description'];
            $purchase_order->status = $data['status'] ?? 1;
            $purchase_order->created_by = auth('api')->id() ?? Auth::id();
            $purchase_order->updated_by = auth('api')->id() ?? Auth::id();

            $purchase_order->save();
            if (isset($data['items']) && is_array($data['items'])){
                foreach ($data['items'] as $item){
                    if (isset($item['id'])){
                        $purchase_order_item = PurchaseOrderItem::find($item['id']);

                        $purchase_order_item->item_id = $item['item_id'];
                        $purchase_order_item->quantity = $item['quantity'];
                        $purchase_order_item->approved_quantity = ($item['approved_quantity'] ?? $item['quantity'] ?? null);
                        $purchase_order_item->package_id = $item['package_id'] ?? null;
                        $purchase_order_item->package_quantity = $item['package_quantity'] ?? 1;
                        $purchase_order_item->total_quantity = $item['total_quantity'] ?? (($item['approved_quantity'] ?? $item['quantity'] ?? 0) * ( $item['package_quantity'] ?? 1));
                        $purchase_order_item->unit_price = $item['unit_price'];
                        $purchase_order_item->total_price = $item['total_price'] ?? (!is_null($item['unit_price'])) ? ($item['unit_price'] * ($item['approved_quantity'] ?? $item['quantity'] ?? 0))  :  null;
                        $purchase_order_item->status = 1;
                        $purchase_order_item->updated_by = auth('api')->id() ?? Auth::id();
                        
                        $purchase_order_item->save();
                    }
                    else{
                        PurchaseOrderItem::create([
                            'po_id' => $purchase_order->id,
                            'item_id' => $item['item_id'],
                            'quantity' => $item['quantity'],
                            'approved_quantity' => ($item['approved_quantity'] ?? $item['quantity'] ?? null),
                            'package_id' => $item['package_id'] ?? null,
                            'package_quantity' => $item['package_quantity'] ?? 1,
                            'total_quantity' => $item['total_quantity'] ?? (($item['approved_quantity'] ?? $item['quantity'] ?? 0) * ( $item['package_quantity'] ?? 1)),
                            'unit_price' => $item['unit_price'] ?? null,
                            'total_price' => $item['total_price'] ?? (!is_null($item['unit_price'])) ? ($item['unit_price'] * ($item['approved_quantity'] ?? $item['quantity'] ?? 0))  :  null,
                            'status' => 1,
                            'created_by' => auth('api')->id() ?? Auth::id(),
                            'updated_by' => auth('api')->id() ?? Auth::id(),
                        ]);
                    }

                    array_push($active_items, $item['item_id']);
                }
            }

            PurchaseOrderItem::where('po_id', '=', $purchase_order)->whereNotIn('item_id', $active_items)->delete();

            $this->log_user_activity('Procurement Purchase Order Update', $id, true);
            DB::commit();
            return $purchase_order;
        }   
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Procurement Purchase Order Update', $id, false);
            return $e->getMessage();
        } 
        
    }

    /*
    ---------------------------------------------------------------------------------------------------------
    Purchase Order Item Basic Functions 
    ---------------------------------------------------------------------------------------------------------
    */
    public function procurement_purchase_order_item_create($data){
        if (!isset($data['po_id'])){
            return "Invalid Purchase Order";
        }
        else{
            $query = PurchaseOrder::where('unique_id', '=',$data['po_id'])->orWhere('id', '=', $data['po_id'])->first();
            //Check that the Purchase Order Exists

            //Purchase Order does not exist return error
            if (is_null($query) || ($query->status > 2 && $query->status < 10)){
                return "Invalid Purchase Order";
            }
            else{//It exists, 
                $quest = PurchaseOrderItem::where('po_id', '=', $data['po_id'])->where('item_id', '=', is_array($data['item_id']) ? $data['item_id']['id'] : $data['item_id'])->first();
                if ($quest){// now check if the item already exists on this Purchase Order
                    return "Item already exists in purchase order";
                }
                else{
                    $purchase_order_item = PurchaseOrderItem::create([
                        'po_id' => $data['po_id'],
                        'item_id' => is_array($data['item_id']) ? $data['item_id']['id'] : $data['item_id'],
                        'item_name' => is_array($data['item_id']) ? $data['item_id']['name'] : 'Undefined Product',
                        'quantity' => $data['quantity'],
                        'approved_quantity' => ($data['approved_quantity'] ?? $data['quantity'] ?? 0),
                        'package_id' => $data['package_id'] ?? 1,
                        'package_quantity' => $data['package_quantity'] ?? 1,
                        'total_quantity' => $data['total_quantity'] ?? (($data['approved_quantity'] ?? $data['quantity'] ?? 0) * ( $data['package_quantity'] ?? 1)),
                        'unit_price' => $data['unit_price'] ?? 0,
                        'total_price' => $data['total_price'] ?? (!is_null($data['unit_price'])) ? ($data['unit_price'] * ($data['approved_quantity'] ?? $data['quantity'] ?? 0))  :  0,
                        'status' => 1,
                        'created_by' => auth('api')->id() ?? Auth::id(),
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]);
                }
                $query->status = $query->status == 10 ? 0 : $query->status;
                $query->updated_by = auth('api')->id() ?? Auth::id();
                $query->save();
            }
        }
        $item = PurchaseOrderItem::where('id', '=', $purchase_order_item->id)->with('item')->first();
        return $item;
    }

    public function procurement_purchase_order_item_delete($id){
        $query = PurchaseOrderItem::find($id);

        $query->status = 0;
        $query->updated_by = auth('api')->id() ?? Auth::id();
        $query->deleted_by = auth('api')->id() ?? Auth::id();
        $query->deleted_at = date('Y-m-d H:i:s');
        
        $query->save();
    }

    public function procurement_purchase_order_item_update($data, $id){
        $query = PurchaseOrderItem::find($id);

        $query->po_id = $data['po_id'];
        $query->item_id = $data['item_id'];
        $query->quantity = $data['quantity'];
        $query->approved_quantity = ($data['approved_quantity'] ?? $data['quantity'] ?? 0);
        $query->package_id = $data['package_id'] ?? 1;
        $query->package_quantity = $data['package_quantity'] ?? 1;
        $query->total_quantity = (($data['approved_quantity'] ?? $data['quantity'] ?? 0) * ( $data['package_quantity'] ?? 1));
        $query->unit_price = $data['unit_price'] ?? 0;
        $query->total_price = (!is_null($data['unit_price'])) ? ($data['unit_price'] * ($data['approved_quantity'] ?? $data['quantity'] ?? 0))  :  0;
        $query->status = 1;
        $query->updated_by = auth('api')->id() ?? Auth::id();
        
        $query->save();
    }
}