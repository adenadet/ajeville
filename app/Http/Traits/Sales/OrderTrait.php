<?php
namespace App\Http\Traits\Sales;

use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Finance\ExpenseTrait;
use App\Http\Traits\Finance\IncomeTrait;
use App\Http\Traits\Finance\MainTransactionTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Procurement\PurchaseOrderTrait;
use App\Models\CRM\Customer;
use App\Models\Finance\Expense;
use App\Models\Finance\Income;
use App\Models\Finance\PriceList;
use App\Models\Inventory\Item;
use App\Models\Inventory\OrderFulfillment;
use App\Models\Inventory\StoreItem;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Procurement\Batch;
use App\Models\Procurement\PaymentTerm;
use App\Models\Sales\DeliveryNote;
use App\Models\Sales\DeliveryNoteItem;
use App\Models\Sales\Order;
use App\Models\Sales\OrderApproval;
use App\Models\Sales\OrderItem;
use App\Models\Sales\OrderReturn;
use App\Models\Sales\OrderReturnItem;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationItem;
use App\Services\Finance\OrderIncomeService;
use App\Services\Inventory\IssuanceService;
use App\Services\Sales\OrderFulfillmentService;
use App\Services\Sales\UniqueIDService;
use App\Services\Sales\OrderService;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


trait OrderTrait {
    use CustomerTrait, ExpenseTrait, FileManagerTrait, IncomeTrait, MainTransactionTrait, LogTrait, PurchaseOrderTrait, StoreTrait; 
    //use IncomeTrait;


    /*
    -----------------------------------------------------------------------------------------------
    Sales Fulfillment Functions
    -----------------------------------------------------------------------------------------------
    */ 

    public function sales_order_item_fulfill($data, $type = 'sales'){
        try{
            $unique_id = new UniqueIDService();

            $fulfiller = new OrderFulfillmentService();
            $fulfillment = $fulfiller->fulfill_item_manually([
                'type' => $type,
                'batch_id'      => $data['batch_id'],
                'quantity'      => $data['quantity'],
                'referenceable_type' => OrderItem::class,
                'reference_id'  => $data['order_item_id'],
                'store_item_id' => $data['store_item_id'],
            ], $data['order_item_id']);
            return $fulfillment;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------------------------------
    Sales Delivery Functions
    -----------------------------------------------------------------------------------------------
    */ 
    public function sales_goods_delivered_by($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'fulfillment':
                $query = OrderItem::where('po_id', '=', $specific);
            break;
        }
        $query = $detailed ? $query->with(['batches', 'item', 'purchase_order']) : $query->select('id')->with(['batches']);
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query; 
    }

    public function sales_goods_delivered_confirm($data, $id){
        DB::beginTransaction();

        try{
            $purchase_order = Order::find($id);
            
            foreach ($data['items'] as $order_item){
                foreach($order_item['batches'] as $batch){
                    $query = Batch::find($batch['id']);

                    $query->status = $batch['status'];
                    $query->confirmed_by = Auth::id() ?? auth('api')->id();
                    $query->confirmed_at = date('Y-m-d H:i:s');

                    $query->save();
                }
                //check if the total amount received is same as required by PO
                $po_item_qty = OrderItem::find($order_item['po_item_id'])->pluck('total_quantity'); print($po_item_qty);
                $confirmed_items_qty = Batch::where('po_item_id', '=', $order_item['po_item_id'])->where('status', '=', 1)->sum('total_quantity'); echo ($confirmed_items_qty);
                
                
                $po_item = OrderItem::find($order_item['po_item_id']);
                $po_item->status = ($po_item_qty <= $confirmed_items_qty) ? 4 : 3;
                $po_item->updated_by = Auth::id() ?? auth('api')->id();

                $po_item->save();
            }
            $pending = OrderItem::where('po_id', '=', $id)->where('status'. '=', 4)->count();
            
            $purchase_order->status = ($pending == 0) ? 6 : 4;
            $purchase_order->updated_by = auth('api')->id() ?? Auth::id();    
            
            $purchase_order->save();
                
            $this->log_user_activity('Sales Order GDN Confirmed', $id, true); 
            DB::commit();
            return $purchase_order;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Order GDN Confirmed', $id, false);
            return $e->getMessage();
        }
    }
    public function sales_goods_delivered_create($data){
        DB::beginTransaction();
        
        try{
            $query = Order::find($data['order_id']);
            if (($query->status < Order::StatusApproved) || ($query->status == Order::StatusCancelled)){
                return "Invalid Sales Order";
            } 
            else{
                //Create a new delivery note
                $delivery_note = DeliveryNote::create([
                    'uuid' => $this->sales_generate_unique_id('deliverable'),
                    'so_id' => $data['order_id'],
                    'status' => DeliveryNote::StatusCreated,
                    'created_by' => Auth::id() ?? auth('api')->id(),
                    'updated_by' => Auth::id() ?? auth('api')->id(),
                ]);

                //Create delivery items for each item in the delivery note
                foreach ($data['order_items'] as $item){
                    DeliveryNoteItem::create([
                        'delivery_note_id' => $delivery_note->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'created_by' => Auth::id() ?? auth('api')->id(),
                        'updated_by' => Auth::id() ?? auth('api')->id(),
                    ]);   

                    //Update the amount of Items delivered in the Order Item
                    $order_item = OrderItem::find($item['id']);

                    $order_item->delivered_quantity += $item['quantity'];
                    $order_item->updated_by = Auth::id() ?? auth('api')->id();
                    $order_item->save();
                }

                //Update the Status of the Order as Ongoing
                $query->status = Order::StatusOngoing;
                $query->updated_by = Auth::id() ?? auth('api')->id();
                $query->save();
                
            }

            $quest = $this->sales_goods_delivered_get_by(null, $delivery_note->id, true);
            $this->log_user_activity('Sales Order Delivery Note Created', $delivery_note->id, true); 
            DB::commit();
            return $quest;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Order Delivery Note Created', null, false);
            return $e->getMessage();
        }
    } 

    public function sales_goods_delivered_get_all($type, $specific, $detailed, $paginated, $page){
        $query = DeliveryNote::query();
        switch ($type){
            case 'sales_order':
                $query = DeliveryNote::where('so_id', '=', $specific);
            break;    
        }
        $query = $detailed ? $query->with(['order.store']) : $query->select('id');
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query;
    }

    public function sales_goods_delivered_get_by($type, $id, $detailed){
        $query = DeliveryNote::where('id', '=', $id)->orWhere('uuid', '=', $id);
        
        $query = $detailed ? $query->with(['creator', 'delivery_items.item', 'order.customer', 'order.store']) : $query;
        return $query->first();
    }

    /*
    -----------------------------------------------------------------------------------------------
    Sales Additional Costs Functions
    -----------------------------------------------------------------------------------------------
    */

    public function sales_order_additional_cost($data, $id){
        DB::beginTransaction();

        try{
            $procurement = Order::find($id);
            
            $procurement->taxes = $data['taxes'];
            $procurement->logistics = $data['logistics'];
            $procurement->discount = $data['discount'];
            $procurement->updated_by = auth('api')->id() ?? Auth::id();

            $procurement->save();
            
            $this->log_user_activity('Sales Order Additional Cost Modified', $id, true); 
            DB::commit();
            return $procurement;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Order Additional Cost Modified', $id, false);
            return $e->getMessage();
        }
    }

    /*
    -----------------------------------------------------------------------------------------------
    Sales Order Functions
    -----------------------------------------------------------------------------------------------
    */
    public function sales_order_approve($data, $id){
        /*
        ----------------------------------------------------------------------
        This is function will trigger multiple services:
        1. Approve the Sales Order
        2. Approve the Sales Order Item Quantities
        3. Create an Income for the Sales Order / Create Main Transactions for the Sales Order
        4. Fulfill the Stock for the approved Sales Order Items
        5. Return the approved Sales Order
        -----------------------------------------------------------------------
        */
        //Preambles get the Sales Order and Payment Term details
        try{
            $order = $this->sales_order_get_by(null, $id, true);

            if($order->status == Order::StatusApproved){
                return "Order has already been approved";
            }

            $payment_term = PaymentTerm::findOrFail($order->payment_term_id);
            $payment_due_date = new DateTime($order->delivery_date);
            $payment_due_date->modify('+ '.$payment_term->days.' days');
              
            
            //Approve the Sales Order Items (2)
            $order_income = new OrderIncomeService();
            $approved_order = $order_income->approveOrder($order, $data);
            
            //Create Main Transaction for the Sales Order (3)
            if (is_string($approved_order)){
                $this->log_user_activity('Sales Order Approved', $id, false);
                return $approved_order;    
            }
            
            DB::commit();
            $this->log_user_activity('Sales Order Approved', $id, true);
            return $order;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Order Approved', $id, false);
            return $e->getMessage();
        }
    }

    public function sales_order_assign_vendor($data){
        DB::beginTransaction();

        try{
            $procurement = Order::find($data['po_id']);
            
            $procurement->vendor_id = $data['vendor_id'];
            $procurement->updated_by = auth('api')->id() ?? Auth::id();

            $procurement->save();
            
            $this->log_user_activity('Sales Order Customer Assignment', $data['po_id'], true); 
            DB::commit();
            return $procurement;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Order Customer Assignment', null, false);
            return $e->getMessage();
        }
    }

    public function sales_order_complete($id){
        DB::beginTransaction();

        try{
            $order = Order::findOrFail($id);

            if($order->status < Order::StatusApproved){
                DB::rollback();
                return "Order has not been approved";
            }
            else if($order->status == Order::StatusDelivered){
                DB::rollback();
                return "Order has been previously completed";
            }
            else{
                $fulfilled_order = $this->sales_order_fulfillment_auto_fulfill($order->id);
                $delivery_note = DeliveryNote::create([
                    'uuid' => $this->sales_generate_unique_id('deliverable'),
                    'so_id' => $order->id,
                    'status' => DeliveryNote::StatusCreated,
                    'created_by' => Auth::id() ?? auth('api')->id(),
                    'updated_by' => Auth::id() ?? auth('api')->id(),
                ]);

                $order_items = OrderItem::where('so_id', '=', $order->unique_id)->get();
                
                foreach($order_items as $order_item){
                    //echo "Order Item ID: ".$order_item->id." & ";
                    //Fulfill all Items

                    //Create Delivery Note for all items
                    $delivered = DeliveryNoteItem::create([
                        'delivery_note_id' => $delivery_note->id,
                        'item_id' => $order_item->item_id,
                        'quantity' => $order_item->total_quantity - $order_item->delivered_quantity,
                        'created_by' => Auth::id() ?? auth('api')->id(),
                        'updated_by' => Auth::id() ?? auth('api')->id(),
                    ]);

                    //echo "Delivery Item ID: ".$delivered->id."\n";
                    $order_item->delivered_quantity = $order_item->total_quantity; 
                    $order_item->save();
                }

                $delete_delivery_note = DeliveryNoteItem::where('delivery_note_id', '=', $delivery_note->id)->get()->count();
                if ($delete_delivery_note == 0){
                    $delivery_note->deleted_by = auth('api')->id() ?? Auth::id();
                    $delivery_note->deleted_at = date('Y-m-d H:i:s');

                    $delivery_note->save();
                }
                
                $order->status = Order::StatusDelivered;
                $order->save();
                
                return $order;
            }
        }
        catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function sales_order_create($data){
        DB::beginTransaction();

        try{
            if (isset($data['payment_term_id'])){
                $query = PaymentTerm::find($data['payment_term_id']);
                if ($query){$payment_terms = $query->days;}
                else{$payment_terms = 30;}
            }
            else{
                $payment_terms = 30;
            }

            $order = Order::create([
                'unique_id' => $data['unique_id'] ?? $this->sales_generate_unique_id('order'),
                'store_id' => $data['store_id'],
                'customer_id' => $data['customer_id'],
                'customer_lpo' => $data['customer_lpo'] ?? null,
                'type_id' => $data['type_id'] ?? null,
                'payment_term_id' => $data['payment_term_id'],
                'payment_status' => 0,
                'delivery_date' => $data['delivery_date'] ?? NULL,
                'date' => $data['date'] ?? date('Y-m-d'),
                'payment_due_date' => date('Y-m-d', strtotime(  '+ '.$payment_terms.' days', strtotime($data['delivery_date']))),
                'additional_cost' => $data['additional_cost'] ?? null,
                'discount' => $data['discount'] ?? 0.00,
                'taxes' => $data['taxes'] ?? 0.00,
                'logistics' => $data['logistics'] ?? 0.00,
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            foreach ($data['items'] as $item){
                OrderItem::create([
                    'uuid' => $this->sales_generate_unique_id('order_item'),
                    'so_id' => $order->unique_id,
                    'item_name' => $item['item_name'] ?? null,
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'requested_quantity' => $item['quantity'],
                    'approved_quantity' => ($item['approved_quantity'] ?? 0),
                    'package_id' => $item['package_id'] ?? null,
                    'package_quantity' => $item['package_quantity'] ?? 1,
                    'total_quantity' => ($item['package_quantity'] ?? 1) * $item['quantity'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'total_price' => $item['unit_price'] * $item['quantity'] * ($item['package_quantity'] ?? 1),
                    'status' => 1,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
            }

            $sales_order = $this->sales_order_get_by(null, $order->id, true);
            
            $itemsTotal = $sales_order->order_items->sum(function ($item) {
                return (($item->unit_price * $item->quantity) - ($item->discount ?? 0));
            });

            $sales_order->taxes = 0.075 * $itemsTotal;
            $sales_order->save();

            if ($order->type_id == Order::TypePrePaid){
                $income = [
                    'branch_id' => $data['branch_id'] ?? request()->cookie('current_branch'),
                    'incomeable_id' => $order->id, //ID of the reference income
                    'incomeable_type' => 'App\Models\Sales\Order', //Purchase Order, Asset, 
                    'classification_id' => $data['classification_id'] ?? null, //Basically income type
                    'amount' => $sales_order->totalAmount(), //$data['amount'],
                    'payable' => $sales_order->totalAmount(),
                    'date' => $data['date']?? date('Y-m-d'),
                    'due_date' => $data['due_date'] ?? $data['date'] ?? date('Y-m-d'),
                    'vendor_id' => $data['vendor_id'] ?? null,
                    'staff_id' => $data['staff_id'] ?? null,
                    'customer_id' => $data['customer_id'] ?? null,
                    'description' => $data['description'],
                    'status' => Income::StatusConfirmed,
                ];

                $income = $this->finance_income_create($income);

                if (is_string($income)){
                    DB::rollback();
                    $this->log_user_activity('Sales Order Create', null, false);
                    return $income;
                }
            }
            $this->log_user_activity('Sales Order Create', $order->id, true); 
            DB::commit();
            return $order;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Order Create', null, false);
            return $e->getMessage();
        }
    }

    public function sales_order_delete($id){
        DB::beginTransaction();

        try{
            $query = Order::where('unique_id', '=', $id)->orWhere('id', '=', $id)->firstOrFail();
            if ($query->status == 0 || $query->status == 10){
                //$query->status = $query->status 0;
                $query->deleted_by = auth('api')->id() ?? Auth::id();
                $query->deleted_at = date('Y-m-d H:i:s');
                //OrderItem::where('so_id', '=', $id)->delete();
            }
            else{
                $query->status = 10;
                $query->updated_by = auth('api')->id() ?? Auth::id();
            }

            $query->save();

            $this->log_user_activity('Sales Order Delete', $query->id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Order Delete', null, false);
            return $e->getMessage();
        }
    }

    public function sales_order_get_all($type, $specific, $detailed, $paginated, $page){
        $query = Order::query();
        //echo $type;
        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', '>=', 1)->where('status', '<', 10);
            break;
            case 'mine':
                $query = $query->where('created_by', '=', Auth::id() ?? auth('api')->id());
            break;
            case 'quick_search':
                $vendors = Customer::where('name', 'LIKE', "%$specific%")->pluck('id');
                $query = $query->where('unique_id', 'LIKE', "%$specific%")->orWhereIn('vendor_id', $vendors )->withTrashed();
            break;
            case 'search':
                $query = $query->withTrashed();
                
                $question = ($specific['question'] != 0) ? $specific['question'] : null;
                //echo $question;
                //echo ((is_null($question))) ? 'Yes' : 'No';
                if (!is_null($question)){
                    $query = $query->orWhere('unique_id', 'LIKE', "%$question%");
                    //Check if there is a customer with the name
                    $customers = Customer::where('name', 'LIKE', "%$question%")->pluck('id');
                    $query = count($customers) > 0  ? $query->whereIn('customer_id', $customers) : $query;

    
                    /*  ->orWhere('description', 'LIKE', "%$question%")
                        ->orWhere('additional_cost', 'LIKE', "%$question%")
                        ->orWhere('taxes', 'LIKE', "%$question%")
                        ->orWhere('logistics', 'LIKE', "%$question%")
                    */
                }

                $status = $specific['status'] ?? null;
                if (!is_null($status)){
                    $query = ($status == 'all') ? $query->whereNotNull('status') : $query->where('status', '=', $status);
                }
            break;
            case 'status':
                if ($specific == 'all'){
                    $query = $query->whereNotNull('status');
                }
                else{
                    $query = $query->where('status', '=', $specific);
                }
            break;
            case 'this_month':
                $query = $query->whereDate('created_at', '>=', date('Y-m').'-01')->whereDate('created_at', '<=', date('Y-m-d'));
                /*if ($specific['status'] == 'completed'){
                    $query = $query->where('status', '!=', 0);
                }
                else{
                    $query = $query->where('status', '=', $specific['status']);
                }*/
            break;
            case 'unapproved':
                $query = $query->where('status', '=', Order::StatusPending)->where('customer_id', '!=', 0)->whereNotNull('customer_id');
                if (!empty($specific)) {
                    $query->where(function ($q) use ($specific) {
                        $q->where('unique_id', 'like', "%{$specific}%")
                        ->orWhereHas('customer', function ($q2) use ($specific) {
                            $q2->where('name', 'like', "%{$specific}%");
                        });
                    });
                }
            break;
        }
        $query = $query->latest();
        $query = $detailed ? $query->with(['store', 'customer', 'payment_term',]) : $query->select('id', 'unique_id', 'customer_id',);
        $orders = $paginated ? $query->paginate(50) : $query->get();
        
        $orders->each(function ($order) {
            $order->append('grand_amount');
        });
        return $orders;
    }

    public function sales_order_get_by($type, $specific, $detailed){
        switch($type){
            case 'id':
                $query = Order::where('id', '=', $specific);
            break;
            case 'unique_id':
                $query = Order::where('unique_id', '=', $specific);
            break;
            default:
                $query = Order::where('unique_id', '=', $specific)->orWhere('id', '=', $specific);
        }

        $query = $detailed ? $query->withTrashed()->with(['order_items.item', 'order_items.fulfillments.store_item_batch.batch', 'order_items.package', 'creator', 'deleter', 'payment_term', 'store.branch', 'updater', 'customer', ]) : $query->select('id', 'name', 'unique_id');
        return $query->first();
    }
    
    public function sales_order_update($data, $id){
        DB::beginTransaction();
        try{
            $active_items = [];
            if (isset($data['payment_term_id'])){
                $query = PaymentTerm::find($data['payment_term_id']);
                if ($query){
                    $payment_terms = $query->days;
                }
                else{
                    $payment_terms = 30;
                }
            }
            else{
                $payment_terms = 30;
            }
            
            $sales_order = Order::where('unique_id', '=', $id)->orWhere('id', '=', $id)->firstOrFail();
            
            $sales_order->store_id = $data['store_id'];
            $sales_order->customer_id = $data['customer_id'];
            $sales_order->customer_lpo = $data['customer_lpo'] ?? $sales_order->customer_lpo;
            $sales_order->type_id = $data['type_id'];
            $sales_order->payment_term_id = $data['payment_term_id'] ?? $sales_order->payment_term_id;
            $sales_order->delivery_date = $data['delivery_date'] ?? $sales_order->delivery_date;
            $sales_order->date = $data['date'] ?? $sales_order->date;
            $sales_order->payment_due_date = date('Y-m-d', strtotime(  '+ '.$payment_terms.' days', strtotime($data['delivery_date'])));
                
            $sales_order->additional_cost = $data['additional_cost'] ?? $sales_order->additional_cost;
            $sales_order->discount = $data['discount'] ?? $sales_order->discount;
            $sales_order->logistics = $data['logistics'] ?? $sales_order->logistics;
            $sales_order->description = $data['description'];
            $sales_order->status = $data['status'] ?? 1;
            $sales_order->updated_by = auth('api')->id() ?? Auth::id();

            $sales_order->save();

            foreach ($data['items'] as $item){
                if (isset($item['id'])){
                    $sales_order_item = OrderItem::find($item['id']);
                    $sales_order_item->item_id = $item['item_id'];
                    $sales_order_item->quantity = $item['quantity'];
                    $sales_order_item->approved_quantity = ($item['approved_quantity'] ?? $item['quantity'] ?? null);
                    $sales_order_item->package_id = $item['package_id'] ?? 1;
                    $sales_order_item->package_quantity = $item['package_quantity'] ?? 1;
                    $sales_order_item->total_quantity = ($item['quantity'] ?? 0) * ( $item['package_quantity'] ?? 1);
                    $sales_order_item->requested_quantity = ($item['quantity'] ?? 1);
                    $sales_order_item->unit_price = $item['unit_price'];
                    $sales_order_item->total_price = $item['unit_price'] * $item['quantity'] * ($item['package_quantity'] ?? 1);
                    $sales_order_item->status = 1;
                    $sales_order_item->updated_by = auth('api')->id() ?? Auth::id();
                    
                    $sales_order_item->save();
                }
                else{
                    OrderItem::create([
                        'uuid' => $this->sales_generate_unique_id('order_item'),
                        'so_id' => $sales_order->unique_id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'requested_quantity' => $item['quantity'] ?? 1,
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

            OrderItem::where('so_id', '=', $sales_order->unique_id)->whereNotIn('item_id', $active_items)->update([
                'status' => 0,
                'deleted_by' => auth('api')->id() ?? Auth::id(),
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            $this->log_user_activity('Sales Order Update', $id, true);
            DB::commit();
            return $sales_order;
        }   
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Sales Order Update', $id, false);
            return $e->getMessage();
        }    
    }

    public function sales_order_sales_trends($period_type, $number){
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
        
        //ROUND(SUM(price), 2) 
        $query = Order::select([
                DB::raw($query_sql." as label"),
                DB::raw("ROUND(SUM((SELECT SUM((ROUND(oi.unit_price, 2) * oi.total_quantity) - ROUND(oi.discount, 2)) FROM sales_order_items oi WHERE oi.so_id = sales_orders.unique_id)), 2) as base_amount"), 
                DB::raw("SUM(logistics) as logistics"),
                DB::raw("SUM(additional_cost) as additional"),
                DB::raw("SUM(discount) as discounts"),
        ]);
            
        //$sales = $query->get();

        //return response()->json($sales);
        
        switch($period_type){
            case 'day':
                $query->whereDate('created_at', '>=', Carbon::now()->subDays($number));
            break;
            case 'month':
                $query->whereDate('created_at', '>=', Carbon::now()->subMonths($number));
            break;
            case 'week':
                $query->whereDate('created_at', '>=', Carbon::now()->subWeeks($number));
            break;
        }

        $query->orderBy('label')->groupBy('label');
        
        return $query->get();
    }

    public function sales_order_sales_trends_fixed($period_type, $number){
        $query = Order::select([
                DB::raw("SUM( (SELECT SUM(
                (oi.unit_price * oi.quantity) - oi.discount) FROM sales_order_items oi WHERE oi.so_id = sales_orders.unique_id)) as base_amount"),
                DB::raw("SUM(logistics) as logistics"),
                DB::raw("SUM(additional_cost) as additional"),
                DB::raw("SUM(discount) as discounts"),
        ]);
            
        //$sales = $query->get();

        //return response()->json($sales);
        
        switch($period_type){
            case 'day':
                $query->selectRaw(DB::raw("DATE(created_at) as date"))->whereDate('created_at', '>=', Carbon::now()->subDays($number));
            break;
            case 'month':
                $query->whereDate('created_at', '>=', Carbon::now()->subMonths($number))->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month")->groupBy("month");
            break;
            case 'week':
                $query->whereDate('created_at', '>=', Carbon::now()->subWeeks($number))->selectRaw("YEARWEEK(created_at) as week")->groupBy("week");       
            break;
        }

        return $query->get();
    }

    /*
    -----------------------------------------------------------------------------------------------
    Sales Order Fulfillments Functions
    -----------------------------------------------------------------------------------------------
    */
    public function sales_order_fulfillment_auto_fulfill($so_id){
        DB::beginTransaction();
        
        try{
            echo $so_id;
            $order = $this->sales_order_get_by(null, $so_id, true);
            
            if (($order->status < 2) || ($order->status == 10) || ($order->status == 40)){
                return "Invalid Sales Order";
            }

            else{
                foreach ($order->order_items as $item){
                    $remainingQty = $item->total_quantity - $item->fulfilled_quantity;
                    echo $remainingQty;
                
                    $batches = StoreItemBatch::where('store_item_id', function ($q) use ($order, $item) {
                            $q->select('id')
                            ->from('store_items')
                            ->where('store_id', $order->store_id)
                            ->where('item_id', $item->item_id)
                            ->limit(1);
                        })
                        ->where('balance', '>', 0)
                        ->with('batch')
                        ->orderBy('batch.expiry_date', 'asc')
                        ->lockForUpdate() // prevent race conditions
                        ->get();

                    foreach ($batches as $batch) {
                        if ($remainingQty <= 0) {break;}

                        $takeQty = min($batch->balance, $remainingQty);
                        echo "Taking $takeQty from batch {$batch->batch->batch_number}\n";
                        
                        // Create fulfillment
                        //$store_fulfill = $this->inventory_store_items_reduce_quantity($item->item_id, $order->store_id, $batch->id, 'sold', $takeQty, $item->id);
                        //inventory_store_item_order_fulfillment($data)
                        //echo "Created fulfillment ID: {$store_fulfill->id}\n";

                        OrderFulfillment::create([
                            'uuid' => Str::uuid(),
                            'reference_id' => $item->id,
                            'type' => 'sold',
                            'batch_id' => $batch->id,
                            'quantity' => $takeQty,
                            'created_by' => Auth::id() ?? auth('api')->id(),
                            'updated_by' => Auth::id() ?? auth('api')->id(),
                        ]);

                        //Update Order Item Fulfilled Quantity
                        $item->fulfilled_quantity += $takeQty;
                        $item->save();
                        
                        //Update Store Item Batch
                        $batch->balance -= $takeQty;
                        $batch->updated_by = Auth::id() ?? auth('api')->id();
                        $batch->save();
                        
                        $remainingQty -= $takeQty;
                    }

                    // Optionally mark item as fulfilled/partial
                    if ($remainingQty == 0) {
                        $item->update(['status' => 5]);
                    } 
                    else {
                        $item->update(['status' => 3]);
                    }
                }
            }
            //
            $order->status = 3; //Means it is ongoing
            $order->updated_by = Auth::id() ?? auth('api')->id();
            $order->save();

            DB::commit();
            $this->log_user_activity('Sales Order Fulfilled', $order->id, true); 
            
            return $order;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Order Fulfilled', null, false);
            return $e->getMessage();
        }
    }

    public function sales_order_fulfillment_get_all($type, $specific, $detailed, $paginated, $page){
        switch ($type){
            case 'order_item':
                $query = OrderItem::where('so_id', '=', $specific);
            break;
            default:
                $query = OrderItem::query();
        }
        $query = $detailed ? $query->with(['batches', 'item', 'order.store']) : $query->select('id')->with(['batches']);
        $query = $paginated ? $query->paginate(20) : $query->get();
        return $query; 
    }

    
    /*
    ---------------------------------------------------------------------------------------------------------
    Sales Item Basic Functions 
    ---------------------------------------------------------------------------------------------------------
    */
    public function sales_order_item_create($data){
        if (!isset($data['so_id'])){
            return "Invalid Purchase Order";
        }

        else{
            $query = Order::find($data['so_id']);
            if (is_null($query) || ($query->status > 2 && $query->status < 10)){
                return "Invalid Purchase Order";
            }
            else{
                $query->status = $query->status == 10 ? 0 : $query->status;
                $query->updated_by = auth('api')->id() ?? Auth::id();
                $query->save();
            }
        }

        $purchase_order_item = OrderItem::create([
            'po_id' => $data['so_id'],
            'item_id' => is_array($data['item_id']) ? $data['item_id']['id'] : $data['item_id'],
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

        return $purchase_order_item;
    }

    public function sales_order_item_delete($id){
        $query = OrderItem::find($id);

        $query->status = 0;
        $query->updated_by = auth('api')->id() ?? Auth::id();
        $query->deleted_by = auth('api')->id() ?? Auth::id();
        $query->deleted_at = date('Y-m-d H:i:s');
        
        $query->save();
    }

    public function sales_order_item_update($data, $id){
        $query = OrderItem::find($id);

        $query->so_id = $data['so_id'];
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

    /*
    -----------------------------------------------------------------------------------------------
    Sales Returns Functions
    -----------------------------------------------------------------------------------------------
    */
    /*
    public function sales_return_create($data){
        DB::beginTransaction();

        try{
            $return = OrderReturn::create([
                'unique_id' => $data['unique_id'] ?? $this->sales_generate_unique_id('return'),
                'sales_order_id' => $data['sales_order_id'],
                'date' => $data['date'] ?? date('Y-m-d'),
                'customer_id' => $data['customer_id'],
                'price_list_id' => $data['price_list_id'] ?? null,
                'store_id' => $data['store_id'],
                'status' => OrderReturn::STATUS_CREATED,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            foreach($data['return_items'] as $item){
                OrderReturnItem::create([
                    'return_id' => $return->id,
                    'item_id' => is_array($item['item_id']) ?  $item['item_id']['id'] :  $item['item_id'],
                    'item_name' => $item['item_name'] ?? $item['name'],
                    'quantity' =>  isset($item['quantity']) ?  ($item['quantity']) : 0,
                    'unit_price' => isset($item['unit_price']) ?  ($item['unit_price']) : 0.00,
                    'discount' => isset($item['discount']) ?  ($item['discount']) : 0.00,
                    'reason' => isset($item['reason']) ?  ($item['reason']) : null,
                    'status' => OrderReturnItem::STATUS_CREATED,
                ]);
            }

            DB::commit();
            $this->log_user_activity('Sales Return Create', null, false);
            return $return;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Return Create', null, false);
            return $e->getMessage();
        }
    }

    public function sales_return_confirm($data, $id){
        DB::beginTransaction();

        try{
            $return = OrderReturn::where('id', '=', $id)->orWhere('unique_id', '=', $id)->first();
            
            // 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at', 'status', 'reason', 'confirmed_by', 'confirmed_at', 'confirm_remark'
            if (is_null($return)){
                DB::rollback();
                $this->log_user_activity('Sales Return Confirmed', $id, false);
                return 'Invalid Sales Return';
            }
            if ($return->status != 1){
                DB::rollback();
                $this->log_user_activity('Sales Return Confirmed', $id, false);
                return 'Sales Return already confirmed';
            }
            else{
                $return->status = $data['decision'] == 'confirm' ? OrderReturn::STATUS_CONFIRMED : OrderReturn::STATUS_REJECTED;
                $return->updated_by = auth('api')->id() ?? Auth::id();

                $return_items = OrderReturnItem::where('return_id', '=', $return->id)->with(['return_batches'])->get();
                foreach ($return_items as $r_item){
                    if (!empty($return_items->return_batches)){
                        foreach($return_items->return_batches as $batch){
                            $coast = $this->inventory_store_items_increase_quantity($r_item['item_id'], $return['store_id'], $batch['batch_id'], $r_item['quantity']);
                            if (is_string($coast)){
                                DB::rollback();
                                $this->log_user_activity('Sales Return Confirmed', $id, false);
                                return $coast;
                            }
                        }
                    }
                    else{
                        $coast = $this->inventory_store_items_increase_quantity($r_item['item_id'], $return['store_id'], null, $r_item['quantity']);
                        if (is_string($coast)){
                            DB::rollback();
                            $this->log_user_activity('Sales Return Confirmed', $id, false);
                            return $coast;
                        }
                    }
                }
            }
             
            $return->save();
            DB::commit();
            $this->log_user_activity('Sales Return Confirmed', $id, true);

            return $return;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Return Confirmed', $id, false);
            return $e->getMessage();
        }
    }

    public function sales_return_delete($id){
        DB::beginTransaction();

        try{
            $query = OrderReturn::where('unique_id', '=',$id)->orWhere('id', '=', $id)->firstOrFail();

            if ($query->confirmed_at) {
                DB::rollback();
                $this->log_user_activity('Sales Return Delete', $id, false);
                return 'Confirmed return cannot be deleted';
            }

            $query->status = OrderReturn::STATUS_REJECTED;
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();

            $this->log_user_activity('Sales Return Delete', $id, true); 
            DB::commit();
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Return Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function sales_return_update($data, $id){
        DB::beginTransaction();
        try{
            $return = OrderReturn::where('unique_id', '=', $id)->orWhere('id', '=', $id)->first();
            
            $return->update([
                'customer_id'       => $data['customer_id'],
                'date'              => $data['date'] ?? date('Y-m-d'),
                'description'       => $data['description'] ?? null,
                'price_list_id'     => $data['price_list_id'],
                'sales_order_id'    => $data['sales_order_id'],
                'status'            => $data['status'] ?? OrderReturn::STATUS_CREATED,
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $incomingItemIds = collect($data['return_items'])
                ->pluck('id')
                ->filter()
                ->toArray();
                
            OrderReturnItem::where('return_id', $return->id)->whereNotIn('id', $incomingItemIds)->whereNull('deleted_at')->update(['deleted_at' => now(),]);

            foreach ($data['return_items'] as $item){
                $payload = [
                    'return_id'  => $return->id,
                    'item_id'    => is_array($item['item_id']) ? $item['item_id']['id']: $item['item_id'],
                    'item_name'  => $item['item_name'] ?? $item['name'],
                    'quantity'   => $item['quantity'] ?? 0,
                    'unit_price' => $item['unit_price'] ?? 0.00,
                    'discount'   => $item['discount'] ?? 0.00,
                    'reason'     => $item['reason'] ?? null,
                    'status'     => OrderReturnItem::STATUS_CREATED,
                    'deleted_at' => null, // restore if previously deleted
                ];
                
                if (isset($item['id'])){
                    //update existing item
                    OrderReturnItem::where('id', '=', $item['id'])
                    ->where('return_id', '=', $return->id)
                    ->withTrashed()->first()->update($payload);
                }
                else{
                    OrderReturnItem::create($payload);
                }
            }

             
            $return->save();
            DB::commit();
            $this->log_user_activity('Sales Return Update', $id, true);

            return $return;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Return Update', $id, false);
            return $e->getMessage();
        }
    }
    */

    /*
    -----------------------------------------------------------------------------------------------
    Sales Quotations Functions
    -----------------------------------------------------------------------------------------------
    */

    public function sales_quotation_create($data){
        DB::beginTransaction();

        try{
            $quotation = Quotation::create([
                'uuid' => $this->sales_generate_unique_id('quotation'), 
                'customer_id' => $data['customer_id'] ?? 0, 
                'quote_date' => $data['quote_date'] ?? date('Y-m-d'),
                'payment_term_id' => $data['payment_term_id'] ?? null,
                'store_id' => $data['store_id'] ?? null,
                'expiry_date' => $data['expiry_date'] ?? date('Y-m-d',
                strtotime("+30 days")),
                'status'=> $data['status'] ?? 'draft',
                'logistics' => $data['logistics'] ?? 0.00,
                'discount' => $data['discount'] ?? 0.00,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            foreach ($data['items'] as $item){
                $this->sales_quotation_item_create($item, $quotation->uuid);
            }
            
            DB::commit();
            $this->log_user_activity('Sales Quotation Created', $quotation->id, true);
            return $quotation;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Quotation Created', null, false);
            return $e->getMessage();
        }
    }

    public function sales_quotation_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'all':
                $query = Quotation::withTrashed();
            break;
            case 'customer_id':
                $query = Quotation::where('customer_id', '=', $specific);
            break;
            case 'customers':
                $customers = Customer::where('name', 'LIKE', "%$specific%")->pluck('uuid');
                $query = Quotation::WhereIn('customer_id', $customers);
            break;
            case 'mine':
                $query = Quotation::where('created_by', '=', Auth::id() ?? auth('api')->id());
            break;
            case 'status':
                if ($specific == 'all'){
                    $query = Quotation::whereIn('status', ['draft', 'sent', 'agreed', 'overdue', 'cancelled']);
                }
                else{
                    $query = Quotation::where('status', '=', $specific);
                }
            break;
        }

        $query = $query->latest();
        $query = $detailed ? $query->with(['customer', 'creator', 'updater']) : $query->select('id', 'name', 'uuid');
        $query = $paginated ? $query->paginate($page) : $query->get();
        
        return $query;
    }

    public function sales_quotation_get_by($type, $id, $detailed){
        switch($type){
            case 'id':
                $query = Quotation::where('id', '=', $id);
            break;
            case 'uuid':
                $query = Quotation::where('uuid', '=', $id);
            break;
            default:
                $query = Quotation::where('id', '=', $id)->orWhere('uuid', '=', $id);
        }

        $query = $detailed ? $query->with(['customer', 'creator', 'updater', 'quotation_items.item', 'quotation_items.package']) : $query->select('id', 'name', 'uuid');
        
        return $query->first();
    }

    public function sales_quotation_update($data, $id){
        DB::beginTransaction();
        try{
            $quotation = Quotation::where('uuid', '=', $id)->firstOrFail();
            $quote_items = QuotationItem::where('quotation_id', '=', $id)->get();

            $data['updated_by'] = Auth::id() ?? auth('api')->id();
            $items = collect($data->items);
            // Extract uuids from incoming items
            $incomingUuids = $items->pluck('uuid')->filter()->all();

            foreach ($items as $quote_item) {
                $uuid = $quote_item['uuid'] ?? null;
                // Case 1: New item to be created
                if (is_null($uuid) && isset($quote_item['id'])) {
                    $this->sales_quotation_item_create($quote_item, $id);
                } 
                // Case 2: Existing item to be updated
                elseif (!is_null($uuid) && in_array($uuid, $incomingUuids)) {
                    // Find the incoming item data
                    $incomingItem = $items->firstWhere('uuid', $uuid);
                    $this->sales_quotation_item_update($incomingItem, $uuid);
                }    
                // Case 3: Item to be deleted
                elseif (is_null($uuid) || !in_array($uuid, $incomingUuids)) {
                    $this->sales_quotation_item_delete($quote_item, $uuid);
                }
            }
            $quotation->update($data->all());
            DB::commit();
            $this->log_user_activity('Sales Quotation Updated', $id, true);
            return $quotation;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Sales Quotation Updated', $id, false);
            return $e->getMessage();
        }
    }  

    /*
    -----------------------------------------------------------------------------------------------
    Sales Quotation Item Functions
    -----------------------------------------------------------------------------------------------
    */

    public function sales_quotation_item_create($data, $id){
        try{
            $item = QuotationItem::create([
                'uuid' => $this->sales_generate_unique_id('quotation'), 
                'quotation_id' => $id, 
                'item_id' => $data['item_id'] ?? null,
                'item_name' => $data['item_name'] ?? null,
                'description' => $data['item_name'] ?? 'A new item or product', 
                'quantity' => $data['quantity'] ?? 1,
                'unit_price' => $data['unit_price'] ?? 1.00,
                'total_price' => ($data['quantity'] ?? 1) * ($data['unit_price'] ?? 1.00),
            ]);

            return $item;
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function sales_quotation_item_delete($data, $id){
        try{
            $item = QuotationItem::where('uuid', '=', $id)->first();

            $item->deleted_by = auth('api')->id() ?? Auth::id();
            $item->deleted_at = date('Y-m-d H:i:s');
            $
            $item->update($data);

            return $item;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function sales_quotation_item_get_all($id){
        $query = QuotationItem::where('quotation_id', '=', $id)->get();

        return $query;
    }
    public function sales_quotation_item_update($data, $id){
        try{
            $item = QuotationItem::where('uuid', '=', $id)->first();

            $item->update($data);
            return $item;
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

        
    /*
    |--------------------------------------------------------------------------
    | RETURN VALUATION
    |--------------------------------------------------------------------------
    */

    protected function sales_return_calculate_amount($id): float
    {
        $total = 0;
        $return = $this->sales_return_get_by('id', $id, true);
        foreach ($return->return_items as $item) {

            /*
             * Priority:
             * 1. Sales order price
             * 2. Price list price
             */
            //$price_list = PriceList
            if ($return->sales_order_id) {
                $price = $item->sales_order_item->price ?? 0;
            } 
            else {
                $price = $item->unit_price ??0.00;
            }
            $total += ($price * $item->quantity);
        }

        return $total;
    }

    public function sales_return_confirm($data, $id)
    {
        return DB::transaction(function () use ($id) {

            $userId = auth('api')->id() ?? Auth::id();

            $return = OrderReturn::with(['return_items.return_batches'])
                ->lockForUpdate()
                ->findOrFail($id);

            if ($return->confirmed_at) {
                throw new \RuntimeException('Sales Return already confirmed.');
            }

            foreach ($return->return_items as $returnItem) {

                $storeItem = StoreItem::where('store_id', $return->store_id)
                    ->where('item_id', $returnItem->item_id)
                    ->first();

                if (!$storeItem) {
                    throw new \RuntimeException(
                        "StoreItem missing for item_id {$returnItem->item_id} in store {$return->store_id}"
                    );
                }

                if ($return->legacy_return || is_null($return->sales_order_id)) {

                    $batch = Batch::create([
                        'unique_id'        => $this->procurement_unique_id('goods_received'),
                        'item_id'          => $returnItem->item_id,
                        'package_id'       => 1,
                        'package_quantity' => $returnItem->quantity,
                        'batch_number'     => 'LEGACY-' . sprintf('%07d', $returnItem->item_id),
                        'expiry_date'      => null,
                        'quantity'         => $returnItem->quantity,
                        'total_quantity'   => $returnItem->quantity,
                        'status'           => Batch::StatusConfirmed,
                        'confirmed_by'     => $userId,
                        'confirmed_at'     => now(),
                        'created_by'       => $userId,
                        'updated_by'       => $userId,
                    ]);

                    StoreItemBatch::create([
                        'store_item_id' => $storeItem->id,
                        'batch_id'      => $batch->id,
                        'balance'       => $returnItem->quantity,
                        'received'      => $returnItem->quantity,
                        'sold'          => 0,
                        'issued'        => 0,
                        'transferred'   => 0,
                        'status'        => 1,
                        'created_by'    => $userId,
                        'updated_by'    => $userId,
                    ]);

                }
                else {

                    foreach ($returnItem->return_batches as $returnBatch) {

                        $storeItemBatch = StoreItemBatch::where('store_item_id', $storeItem->id)
                            ->where('batch_id', $returnBatch->id)
                            ->lockForUpdate()
                            ->first();

                        if (!$storeItemBatch) {
                            throw new \RuntimeException(
                                "StoreItemBatch missing for batch {$returnBatch->id}"
                            );
                        }
                        $storeItemBatch->update([
                            'balance'    => $storeItemBatch->balance + $returnBatch->quantity,
                            'sold'       => $storeItemBatch->sold - $returnBatch->sold,
                            'updated_by' => $userId,
                        ]);
                    }
                }
            }

            $amount = $this->sales_return_calculate_amount($return->id);

            if ($amount > 0) {
                $customer = Customer::lockForUpdate()->findOrFail($return->customer_id);
                $customer->update([
                    'balance'    => $customer->balance - $amount,
                    'updated_by' => $userId,
                ]);

                $expense = $this->finance_expense_create([
                    'expenseable_id'   => $return->id,
                    'expenseable_type' => OrderReturn::class,
                    'classification_id'=> null,
                    'amount'           => $amount,
                    'date'             => now()->toDateString(),
                    'due_date'         => now()->toDateString(),
                    'account_id'       => null,
                    'vendor_id'        => null,
                    'staff_id'         => null,
                    'customer_id'      => $return->customer_id,
                    'description'      => 'Customer Return',
                    'status'           => Expense::StatusConfirmed,
                ]);

                if (is_string($expense)) {
                    throw new \RuntimeException($expense);
                }
            }
            $return->update([
                'status'       => OrderReturn::STATUS_CONFIRMED,
                'confirmed_by' => $userId,
                'confirmed_at' => now(),
            ]);
            $this->log_user_activity('Sales Return Confirm', $id, true);
            return $return;
        });
    }


    public function sales_return_create($data)
    {
        DB::beginTransaction();

        try {
            $return = OrderReturn::create([
                'sales_order_id'    => $data['sales_order_id'] ?? null,
                'unique_id'         => $this->sales_generate_unique_id('return'),
                'date' => $data['date'] ?? date('Y-m-d'),
                'customer_id'       => $data['customer_id'],
                'store_id'          => $data['store_id'],
                'price_list_id'     => $data['price_list_id'] ?? null,
                'reason'            => $data['reason'] ?? null,
                'status'            => OrderReturn::STATUS_CREATED,
                'is_legacy_return'  => empty($data['sales_order_id']),
                'created_by'        => auth('api')->id() ?? Auth::id(),
                'updated_by'        => auth('api')->id() ?? Auth::id(),
            ]);

            foreach ($data['return_items'] as $item) {
                $detailed_item = Item::find($item['item_id']);
                $returnItem = OrderReturnItem::create([
                    'return_id'       => $return->id,
                    'item_id'         => $item['item_id'],
                    'item_name'       => $item['item_name'] ??$detailed_item->name ?? 'Old Stock Item',
                    'unit_price'      => $item['unit_price'] ?? 0.00,
                    'quantity'        => $item['quantity'],
                    'reason'          => $item['reason'] ?? null,
                    'status'          => OrderReturnItem::STATUS_CREATED,
                ]);

                if (!$return->is_legacy_return && !empty($item['batches'])) {
                    foreach ($item['batches'] as $batch) {
                        $returnItem->return_batches()->create([
                            'batch_id' => $batch['batch_id'],
                            'quantity' => $batch['quantity'],
                        ]);
                    }
                }
            }

            DB::commit();
            $this->log_user_activity('Sales Return Create', $return->id, true);
            return $return;

        } 
        catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Sales Return Create', null, false);
            return $e->getMessage();
        }
    }

    public function sales_return_delete($id)
    {
        DB::beginTransaction();
        try {
            $return = OrderReturn::where('id', '=', $id)->orWhere('unique_id', '=', $id)->first();
            if ($return->confirmed_at) {
                return 'Sales Return already confirmed.';
            }

            else{
                $return->status = OrderReturn::STATUS_REJECTED;
                $return->deleted_by = auth('api')->id() ?? Auth::id();
                $return->deleted_at = date('Y-m-d H:i:s');
                $return->save();
            }
        
            DB::commit();
            $this->log_user_activity('Sales Return Delete', $id, true);
            return $return;

        } 
        catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Sales Return Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function sales_return_get_all($type, $specific, $detailed, $paginated, $page){
        $query = OrderReturn::query();
        switch($type){
            case 'all':
                $query = OrderReturn::withTrashed();
            break;
            case 'approved':
                $query = OrderReturn::where('status', '>=', OrderReturn::STATUS_CONFIRMED);
            break;
            case 'active':
                $query = OrderReturn::where('status', '>=', 1)->where('status', '<', OrderReturn::STATUS_REJECTED);
            break;
            case 'cancelled':
                $query = OrderReturn::where('status', '>=', OrderReturn::STATUS_REJECTED)->withTrashed();
            break;
            case 'unapproved':
                $query = OrderReturn::where('status', '=', OrderReturn::STATUS_CREATED);
            break;
        }

        if (isset($specific['status'])){
            if ($specific['status'] == 'all'){
                $query = $query->whereNotNull('status');
            }
            else{
                $query = $query->where('status', '=', $specific['status']);
            }
        }
        $query = $query->latest();
        $query = $detailed ? $query->with(['store', 'customer', 'returnItems']) : $query->select('id', 'unique_id');
        
        $orders = $paginated ? $query->paginate(50) : $query->get();
        
        
        return $orders;
    }

    public function sales_return_get_by($type, $id, $detailed){
        try{
            $query = OrderReturn::query();
            switch($type){
                case 'id':
                    $query = OrderReturn::where('id', '=', $id);
                break;
                case 'unique_id':
                    $query = OrderReturn::where('uuid', '=', $id);
                break;
            }

            $query = $query->where('id', '=', $id)->orWhere('unique_id', '=', $id);

            $query = $detailed ? $query->with(['customer', 'creator', 'deleter', 'return_items', 'store', 'updater']) : $query->select('id', 'name', 'unique_id');
            return $query->firstOrFail();
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    public function sales_return_update($data, $id)
    {
        DB::beginTransaction();

        try {
        /*    
            $return = OrderReturn::where('unique_id', '=', $id)->orWhere('id', '=', $id)->first();
                'sales_order_id'    => $data['sales_order_id'] ?? null,
                'customer_id'       => $data['customer_id'],
                'store_id'          => $data['store_id'],
                'price_list_id'     => $data['price_list_id'] ?? null,
                'reason'            => $data['reason'] ?? null,
                'status'            => OrderReturn::STATUS_CREATED,
                'is_legacy_return'  => empty($data['sales_order_id']),
                'created_by'        => auth('api')->id() ?? Auth::id(),
                'updated_by'        => auth('api')->id() ?? Auth::id(),
            ]);

            foreach ($data['items'] as $item) {
                $returnItem = OrderReturnItem::create([
                    'order_return_id' => $return->id,
                    'item_id'         => $item['item_id'],
                    'quantity'        => $item['quantity'],
                    'reason'          => $item['reason'] ?? null,
                ]);

                if (!$return->is_legacy_return && !empty($item['batches'])) {
                    foreach ($item['batches'] as $batch) {
                        $returnItem->return_batches()->create([
                            'batch_id' => $batch['batch_id'],
                            'quantity' => $batch['quantity'],
                        ]);
                    }
                }
            }

            DB::commit();
            $this->log_user_activity('Sales Return Create', $return->id, true);
            return $return;
        */
        } 
        catch (Exception $e) {
            DB::rollBack();
            $this->log_user_activity('Sales Return Create', null, false);
            return $e->getMessage();
        }
    }

}