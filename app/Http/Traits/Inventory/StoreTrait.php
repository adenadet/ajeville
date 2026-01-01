<?php
namespace App\Http\Traits\Inventory;

use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\Procurement\Batch;
use App\Models\Inventory\Item;
use App\Models\Inventory\OrderFulfillment;
use App\Models\Inventory\Store;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Inventory\StoreItem;
use App\Models\Inventory\StoreUser;
use App\Models\Sales\Order;
use App\Models\Sales\OrderItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
trait StoreTrait {
    use FileManagerTrait, LogTrait;

    /*
    -------------------------------------------------------------------------------
    Store 
    -------------------------------------------------------------------------------
    */
    public function inventory_store_create_new($data){
        DB::beginTransaction();

        try{
            $store = Store::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'status' => $data['status'] ?? 1,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            //Log this activitiy
            $this->log_user_activity('Store Create', $store->id, true);

            DB::commit();
            return $store;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Store Create', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_store_delete($id){
        DB::beginTransaction();

        try{
            $store = Store::find($id);
            
            $store->status = 0;
            $store->deleted_by = auth('api')->id() ?? Auth::id();
            $store->deleted_at = date('Y-m-d H:i:s');

            $store->save();

            //Log this activitiy
            $this->log_user_activity('Store Delete', $id, true);

            DB::commit();
            return $store;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Store Delete', $id, false);
            return $e->getMessage();
        }
    }

    public function inventory_store_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = Store::where('status', '=', 1);
            break;
            case 'all':
                $query = Store::withTrashed(true);
            break;
            case 'branch':
                $query = Store::where('branch_id', '=', $specific)->orWhere('branch_id', '=', 0);
            break;
            case 'branches':
                $query = Store::whereIn('branch_id', $specific);
            break;
            case 'department':
                $query = Store::where('department_id', '=', $specific);
            break;
            case 'departments':
                $query = Store::whereIn('department_id', $specific);
            case 'inactive':
                $query = Store::where('status', '!=', 1);
            break;
        }

        $query = $detailed ? $query->with(['branch', 'creator', 'deleter', 'department', 'updater']) : $query->select('id', 'name');
        $query = $query->orderBy('name', 'ASC');
        $query = $paginated ? $query->paginate(20) : $query->get();

        return $query;
    }

    public function inventory_store_update($data, $id){
        DB::beginTransaction();

        try{
            $store = Store::find($id);
            
            $store->name = $data['name'];
            $store->description = $data['description'] ?? null;
            $store->branch_id = $data['branch_id'] ?? null;
            $store->department_id = $data['department_id'] ?? null;
            $store->status = $data['status'] ?? 1;
            $store->updated_by = auth('api')->id() ?? Auth::id();
            
            $store->save();

            //Log this activitiy
            $this->log_user_activity('Store Update', $store->id, true);

            DB::commit();
            return $store;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Store Update', $id, false);
            return $e->getMessage();
        }
    }

    /* 
    -----------------------------------------------------------
    Store Items
    -----------------------------------------------------------
    */
    public function inventory_store_items_batches($store_id, $item_id){
        $store_item = StoreItem::where('store_id', '=', $store_id)->where('item_id', '=', $item_id)->first();

        if ($store_item->status == 0){
            return "Not longer available";
        }
        else{
            $batches = StoreItemBatch::where('balance', '>', 0)->where('store_item_id', '=', $store_item->id)->with(['batch'])->get();
            return $batches;
        }
    }
    
    public function inventory_store_items_create($data){
        DB::beginTransaction();

        try{
            $query = StoreItem::firstOrCreate(['item_id' => $data['item_id'], 'store_id' => $data['store_id']], [
                'reorder_level'         => $data['reorder_level'],
                'maximum_level'         => $data['maximum_level'],
                'expiry_notification'   => $data['expiry_notification'],
                'description'           => $data['description'],
                'status'                => $data['status'] ?? 1,
                'created_by'            => Auth::id() ?? auth('api')->id(),
                'updated_by'            => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();
            $this->log_user_activity('Inventory Store Item Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Inventory Store Item Create', null, false);
            return $e->getMessage();
        }
    }
    public function inventory_store_items_get_all($type, $store_id, $specific, $detailed, $paginated, $page){
        $query = StoreItem::where('store_id', '=', $store_id);
        switch($type){
            case 'search':
                $items = Item::query();
                if(isset($specific['name'])){
                    $name = $specific['name'];
                    $items = $items->where('name', 'LIKE', "%$name%");
                }
                if(isset($specific['brand_id']) && !(empty($specific['brand_id']))){
                    $items = $items->where('brand_id', '=', $specific['brand_id']);
                }
                if(isset($specific['category_id'])&& !(empty($specific['category_id']))){
                    $items = $items->where('category_id', '=', $specific['category_id']);
                }
                if(isset($specific['classification_id'])&& !(empty($specific['classification_id']))){
                    $items = $items->where('classification_id', '=', $specific['classification_id']);
                }

                $items = $items->pluck('id');
                //print_r($items);
                $query = $query->whereIn('item_id', $items);
            break;
        }

        $query = $detailed 
                ? $query->with(['batches', 'item.brand', 'item.category', 'item.classification', 'store'])->withSum('batches as total_balance', 'balance')->withSum('batches as total_received', 'received')->withSum('batches as total_sold', 'sold')->withSum('batches as total_transferred', 'transferred')->withSum('batches as total_issued', 'issued')
                : $query->with(['item', 'store']);
        $query = $query->orderBy(Item::select('name')->whereColumn('inventory_items.id', 'inventory_store_item_settings.item_id'));
        $query = $paginated ? $query->paginate(50) : $query->get();
    
        return $query;
    }

    public function inventory_store_items_reset($store_id){
        DB::beginTransaction();

        try{
            $store = Store::findOrFail($store_id);
            $items = Item::pluck('id');

            foreach ($items as $item){
                StoreItem::firstOrCreate(
                    ['item_id' => $item, 'store_id' => $store_id], 
                    [
                        'reorder_level'         => 0,
                        'maximum_level'         => 0,
                        'expiry_notification'   => 0,
                        'created_by'            => Auth::id() ?? auth('api')->id(),
                        'updated_by'            => Auth::id() ?? auth('api')->id(),
                    ]
                );
            }

            DB::commit();
            $this->log_user_activity('Store Item Reset', $store_id, false);
            $store_items = $this->inventory_store_items_get_all(null, $store_id, null, true, true, null);
            return $store_items;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Store Item Reset', $store_id, false);
            return $e->getMessage();
        }
    }
    //Store Item Fulfillable

    public function inventory_store_item_fulfill_order_item($order_item, $store_item, $type){
        


        $this->log_user_activity($type.' Store Item Order Fulfillment', $store_item->id, true);
    }

    public function inventory_store_item_order_fulfillable($order_id){
        $order = Order::where('unique_id', '=', $order_id)->orWhere('id', '=', $order_id)->with(['order_items'])->first();
        $query = StoreItemBatch::where('store_id', $order->store_id)
            ->whereIn('item_id', $order->order_items->pluck('item_id'))
            ->where('balance', '>', 0)
            ->get();

        return $query;
    }

    public function inventory_store_item_order_fulfillment($data){
        DB::beginTransaction();

        try{
            $order = Order::where('unique_id', '=', $data['order_id'])->orWhere('id', '=', $data['order_id'])->firstOrFail();

            foreach ($data['fulfillments'] as $row) {
                $batch = StoreItemBatch::findOrFail($row['batch_id']);

                if ($batch->balance < $row['quantity']) {
                    throw new Exception("Insufficient balance in batch ID {$batch->id}");
                }

                OrderFulfillment::create([
                    'uuid' => Str::uuid(),
                    'reference_id' => $row['so_item_id'],
                    'type' => 'sold',
                    'batch_id' => $row['batch_id'],
                    'quantity' => $row['quantity'],
                    'created_by' => Auth::id() ?? auth('api')->id(),
                    'updated_by' => Auth::id() ?? auth('api')->id(),
                ]);

                $batch->decrement('balance', $row['quantity']);
                $batch->increment('sold', $row['quantity']);
            }

            $order->status = 5;
            $order->save();

            //Log this activity
            DB::commit();
            $this->log_user_activity('Store Item Order Fulfillment', $data['order_id'], true);

            return $order;
        }
        catch(Exception $e){
            DB::rollback();
            $this->log_user_activity('Store Item Order Fulfillment', null, false);
            return $e->getMessage();
        }
    }

    public function inventory_store_items_fulfillable($store_id, $items){
        //echo $items;
        $query = StoreItemBatch::select('batch_id', 'item_id')->selectRaw('sum(balance) as balance')
                //->where('store_id', '=', $store_id)
                ->whereIn('item_id', $items)
                ->where('balance', '>', 0)
                ->with(['batch', 'item',])
                ->groupBy('item_id', 'batch_id')
                ->get();
        
        return $query;
    }
    
    public function inventory_store_items_increase_quantity($item_id, $store_id, $batch_id, $quantity){

        $store_item = StoreItem::where('store_id', '=', $store_id)->where('item_id', '=', $item_id)->first();        
        if (!$store_item){
            $store_item = StoreItem::create([
                'store_id' => $store_id,
                'item_id' => $item_id,
                'reorder_level' => NULL,
                'maximum_level' => NULL,
                'expiry_notification' => 90,
                'description' => NULL,
                'status' => 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
        }

        $store_item_batch = StoreItemBatch::where('store_item_id', '=', $store_item->id)->where('batch_id', '=', $batch_id)->first();

        if (!$store_item_batch){
            $store_item_batch = StoreItemBatch::create([
                'store_item_id' => $store_item->id, 
                'batch_id' => $batch_id ?? 0, 
                'received' => $quantity, 
                'balance' => $quantity, 
                'transferred' => 0,
                'issued' => 0, 
                'sold' => 0,
                'status' => 1,
            ]);
        }
        else{
            $store_item_batch->received += $quantity;
            $store_item_batch->balance += $quantity;
            $store_item_batch->save();
        }
        
        /*
        $settings = StoreItem::where('item_id', '=', $item_id)->where('store_id', '=', $store_id)->first();
        $store_item_batch = StoreItemBatch::where('item_id', '=', $item_id)->where('store_id', '=', $store_id)->where('batch_id', '=', $batch_id)->first();

        if (isset($store_item_batch)){
            $store_item_batch->received += $quantity;
            $store_item_batch->balance += $quantity;

            $store_item_batch->save();
        }
        else{
            $store_item_batch = StoreItemBatch::create([
                'store_id' => $store_id,
                'item_id' => $item_id,
                'batch_id' => $batch_id, 
                'received' => $quantity,
                'balance' => $quantity,
                'transferred' => null,
                'issued' => null,
                'sold' => null,
                'status' => 1,
            ]);
        }

        if (isset($settings)){
            if((!is_null($settings->maximum_level)) && ($settings->maximum_level < $store_item_batch->balance)){

            }
        }
        */

        $this->log_user_activity('Store Item Batch Increase', ['store_id' => $store_id, 'item_id' => $item_id, 'batch_id' => $batch_id, 'quantity' => $quantity], true);

        return $store_item_batch;
    }

    public function inventory_store_items_reduce_quantity($item_id, $store_id, $batch_id, $type, $quantity, $order_id){
        DB::beginTransaction();

        try{
            $store_item_id = StoreItem::where('item_id', '=', $item_id)->where('store_id', '=', $store_id)->first()->id;
            $store_item_batch = StoreItemBatch::where('store_item_id', '=', $store_item_id)->where('id', '=', $batch_id)->where('balance', '>', $quantity)->firstOrFail();
            if (isset($store_item_batch)){
                if($store_item_batch->balance > $quantity){
                    $store_item_batch->balance -= $quantity;
                    switch ($type){
                        case 'issued':
                            $store_item_batch->issued += $quantity;
                        break;
                        case 'sold':
                            //Update Quantity of Store Item Batch sold 
                            $store_item_batch->sold += $quantity;

                            //Update Fulfilled Quantity of Order Item
                            $order_item = OrderItem::where('id', '=', $order_id)->with(['order.customer'])->firstOrFail();
                            $order_item->fulfilled_quantity += $quantity;
                            $order_item->save();
                        break;
                        case 'transferred':
                            $store_item_batch->transferred += $quantity;
                        break;
                    }
                    $store_item_batch->save();
                    $fulfillment = OrderFulfillment::create([
                        'uuid'          => Str::uuid()->toString(),
                        'type'          => $type,
                        'store_item_id' => $store_item_id,
                        'reference_id'  => $order_id,
                        'batch_id'      => $batch_id,
                        'quantity'      => $quantity,
                        'created_by'    => auth('api')->id() ?? Auth::id(),
                        'updated_by'    => auth('api')->id() ?? Auth::id(),
                    ]);

                    DB::commit();
                    //$this->log_user_activity('Store Item Batch Reduce', ['store_id' => $store_id, 'item_id' => $item_id, 'batch_id' => $batch_id, 'quantity' => $quantity, 'transaction' => $type], true);
                    return $fulfillment;
                }
            }
            else{
                //$this->log_user_activity('Store Item Batch Reduce', ['store_id' => $store_id, 'item_id' => $item_id, 'batch_id' => $batch_id, 'quantity' => $quantity, 'transaction' => $type], true);
                
                return 'Insufficient Quantity in Batch';
            }

        }
        catch(Exception $e){
            DB::rollback();
            return 'Invalid Transaction';
        }
    }

    public function inventory_store_items_update($data, $id){
        DB::beginTransaction();
        try{
            $query = StoreItem::findOrFail($id);
            
            $query->reorder_level = $data['reorder_level'] ?? 0;
            $query->maximum_level = $data['maximum_level'] ?? 0;
            $query->expiry_notification = $data['expiry_notification'] ?? 0; 
            $query->description = $data['description'];
            $query->status = $data['status'] ?? 1;
            $query->updated_by = auth('api')->id() ?? Auth::id();
            
            $query->save();

            $this->log_user_activity('Store Item Update', $query->id, true);
            DB::commit();
            return $query;

        }
        catch (Exception $e){
            $this->log_user_activity('Store Item Update', $id, false);
            DB::rollback();
            return $e->getMessage();
        }    
    }

    public function inventory_store_items_settings_reset($id){
        DB::beginTransaction();
        try{
            $query = StoreItem::find($id); 
            $query->deleted_by = auth('api')->id() ?? Auth::id();
            $query->deleted_at = date('Y-m-d H:i:s');
            $query->save();

            $this->log_user_activity('Store Item Setting Reset', $id, true);
            DB::commit();
            return $query;

        }
        catch (Exception $e){
            $this->log_user_activity('Store Item Setting Reset', $id, false);
            DB::rollback();
            return $e->getMessage();
        }     
    }

    
    /*
    -------------------------------------------------------------------------------------------------
    User Store
    -------------------------------------------------------------------------------------------------
    */
    public function inventory_store_user_get($type, $specific, $detailed, $paginated, $page){
        $user_stores = StoreUser::where('user_id', '=', Auth::id() ?? auth('api')->id())->pluck('store_id');
        switch($type){
            case 'my_stores':
                $query = Store::whereIn('id', $user_stores)->select('id', 'name');
                $query = $paginated ? $query->paginate(10) : $query->get();
            break;
            case 'my_expired_items':
                $batches = Batch::whereDate('expiry_date', '<=', date('Y-m-d'))->pluck('id');
                $query = StoreItemBatch::whereIn('batch_id', $batches)->whereIn('store_id', $user_stores);
                $query = $detailed ? $query->with(['item', 'store', 'batch']) : $query;
                $query = $paginated ? $query->paginate(50) : $query->get();
            break;
            case 'my_soon_to_expire_items':
                $query = StoreUser::where('user_id', '=', (Auth::id() ?? auth('api')->id()))
                            ->with(['store.items.batches.batch' => function ($query) {
                                $query->whereNotNull('expiry_date')
                                    ->where('status', 'active')
                                    ->whereNull('deleted_at');
                            }])
                            ->get()
                            ->flatMap(function ($storeUser) {
                                return $storeUser->store->items->flatMap(function ($item) {
                                    return $item->batches->filter(function ($sib) use ($item) {
                                        if (!$sib->batch || !$sib->batch->expiry_date) {
                                            return false;
                                        }
                                        // Check expiry notification
                                        $threshold = Carbon::now()->addDays($item->expiry_notification);
                                        return $sib->batch->expiry_date <= $threshold;
                                    });
                                });
                            });
                $query = $detailed ? $query->with(['item', 'store', 'batch']) : $query;
                $query = $paginated ? $query->paginate(50) : $query->get();
            break;
            case 'my_unique_items':
                $query = StoreItemBatch::whereIn('store_id', $user_stores)->select('item_id')->distinct();
                $query = $detailed ? $query->with(['item.category', 'item.classification']) : $query;
                $query = $paginated ? $query->paginate(50) : $query->get();

            break;
        }
        return $query;
    }

    public function inventory_user_store_item_get($type, $specific = null, $user_id = null, $paginated = false, $page = null){
        $SIB = (new StoreItemBatch)->getTable();
        $SI  = (new StoreItem)->getTable();
        $B   = (new Batch)->getTable();
        $SU  = (new StoreUser)->getTable();

        $userId = $user_id ?? Auth::id() ?? auth('api')->id();

        $query = StoreItemBatch::query()->withTrashed()
            ->from("$SIB as sib")
            ->select('sib.*')
            ->join("$SI as si", 'si.id', '=', 'sib.store_item_id')
            ->join("$B as b", 'b.id', '=', 'sib.batch_id')
            ->join("$SU as su", 'su.store_id', '=', 'si.store_id')
            ->where('su.user_id', $userId)
            ->whereNotNull('b.expiry_date')
            //->with(['storeItem.store', 'storeItem.item', 'batch'])
            ->distinct();

        // Apply filters based on $type
        switch ($type) {
            case 'expired_items':
                $query->whereRaw('b.expiry_date < CURDATE()');
            break;
            case 'soon_to_expire_items':
                $query->whereRaw('b.expiry_date >= CURDATE()')
                      ->whereRaw('b.expiry_date <= DATE_ADD(CURDATE(), INTERVAL si.expiry_notification DAY)');
            break;
            default:
                throw new \InvalidArgumentException("Unknown inventory type: {$type}");
        }

        if (!empty($specific)) {$query->where('si.name', 'like', "%{$specific}%");}

        $query = $query->with(['store_item.store', 'store_item.item.category', 'store_item.item.classification', 'batch']);

        $query = $paginated ?  $query->paginate(50, ['*'], 'page', $page ?? 1) : $query->get();
        return $query;
    }

    public function inventory_report_store_value($type, $specific, $paginate, $detailed){
        $SIB = (new StoreItemBatch)->getTable();        
        $SI  = (new StoreItem)->getTable();
        $I   = (new Item)->getTable();
        $S   = (new Store)->getTable();
        
        switch ($type) {
            case 'batch_wise':
                $query = StoreItemBatch::select(
                    "$SIB.id",
                    "$SIB.store_item_id",
                    "$SIB.batch_id",
                    DB::raw("SUM($SIB.balance * $I.average_landing_cost) as total_value"),
                    DB::raw("SUM($SIB.balance) as total_balance")
                )
                ->join($SI, "$SI.id", "=", "$SIB.store_item_id")
                ->join($I, "$I.id", "=", "$SI.item_id")
                ->groupBy("$SIB.id", "$SIB.store_item_id", "$SIB.batch_id");
            break;

            case 'item_wise':
                $query = StoreItemBatch::select(
                    "$I.id as item_id",
                    DB::raw("SUM($SIB.balance) as total_balance"),
                    DB::raw("($I.average_landing_cost * SUM($SIB.balance)) as total_value")
                )
                ->join($SI, "$SI.id", "=", "$SIB.store_item_id")
                ->join($I, "$I.id", "=", "$SI.item_id")
                ->groupBy("$I.id");
            break;

            case 'summary_wise':
                return StoreItemBatch::join($SI, "$SI.id", "=", "$SIB.store_item_id")
                    ->join($I, "$I.id", "=", "$SI.item_id")
                    ->select(DB::raw("SUM($SIB.balance * $I.average_landing_cost) as grand_total"))
                    ->value("grand_total");

            case 'store_wise':
                $query = StoreItemBatch::select(
                    "$S.id as store_id",
                    DB::raw("SUM($SIB.balance * $I.average_landing_cost) as total_value"),
                    DB::raw("SUM($SIB.balance) as total_balance")
                )
                ->join($SI, "$SI.id", "=", "$SIB.store_item_id")
                ->join($I, "$I.id", "=", "$SI.item_id")
                ->join($S, "$S.id", "=", "$SI.store_id")
                ->groupBy("$S.id");
            break;

            case 'store_item_wise':
                $query = StoreItemBatch::select(
                    "$SI.id as store_item_id",
                    "$SI.store_id",
                    "$SI.item_id",
                    DB::raw("SUM($SIB.balance) as total_balance"),
                    DB::raw("COUNT(DISTINCT $SIB.batch_id) as total_batches"),
                    DB::raw("($I.average_landing_cost * SUM($SIB.balance)) as total_value")
                )
                ->join($SI, "$SI.id", "=", "$SIB.store_item_id")
                ->join($I, "$I.id", "=", "$SI.item_id")
                ->where("$SI.store_id", $specific)
                ->groupBy("$SI.id", "$SI.store_id", "$SI.item_id");
            break;

            // default: throw new \InvalidArgumentException("Invalid type [$type] supplied.");
        }

        // Handle detailed flag
        if ($detailed) {
            if (in_array($type, ['batch_wise', 'item_wise', 'store_item_wise'])) {
                $query->addSelect("$I.name as item_name", "$S.name as store_name");
            } 
            elseif ($type === 'store_wise') {
                $query->addSelect("$S.name as store_name");
            }
        }

        // Handle pagination (skip for summary_wise)
        if ($paginate && $type !== 'summary_wise') {
            return $query->paginate(20);
        } 
        else {
            return $query->get();
        }
    }
}