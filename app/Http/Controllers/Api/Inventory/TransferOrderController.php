<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\VisitTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Inventory\TransferOrderTrait;
use App\Models\Inventory\Category;
use App\Models\Inventory\ItemType;
use App\Models\Inventory\Store;
use App\Models\Inventory\StoreUser;
use Illuminate\Http\Request;

use App\Models\Inventory\TransferOrder;
use App\Models\Inventory\TransferOrderItem;

class TransferOrderController extends Controller
{
    use ItemTrait, StoreTrait, TransferOrderTrait, VisitTrait; 
    public function index()
    {
        if(isset($_GET['query'])){
            $transfer_orders = $this->inventory_transfer_order_get_all('search', ['query' => $_GET['query'], 'status' => $_GET['status'], 't' => $_GET['t'] ?? 'in',], true, true, $_GET['page'] ?? 1);
        }
        else if ($_GET['status'] != 'all'){
            $transfer_orders = $this->inventory_transfer_order_get_all('status', ['status' => $_GET['status'], 't' => $_GET['t'] ?? 'in',], true, true, $_GET['page'] ?? 1);    
        }
        else{
            $transfer_orders = $this->inventory_transfer_order_get_all($_GET['t'] ?? 'in', [], true, true, $_GET['page'] ?? 1);
        }
        return response()->json([
            'transfer_orders' => $transfer_orders,
        ]);
        
    }

    public function initials()
    {
        $user_stores = StoreUser::where('user_id', '=', auth('api')->id())->pluck('store_id');
        $my_stores = Store::select('id', 'name')->whereIn('id', $user_stores)->where('status', '=', 1)->orderBy('name', 'ASC')->get();
        
        return response()->json([
            'categories' => Category::select('id', 'name')->orderBy('name')->get(),
            'items' => $this->inventory_item_get_all('consumable', '', false, false, null),
            'my_stores' => $my_stores,
            'patients_active' => $this->emr_visit_active_patients(request()->cookie('current_branch')),
            'stores' => Store::select('id', 'name')->where('status', '=', 1)->orderBy('name', 'ASC')->get(),
            'types' => ItemType::orderBy('name')->get(),
        ]);        
    }

    public function my_store_requests(){
        $user_stores = StoreUser::where('user_id', '=', auth('api')->id())->pluck('store_id');
        $transfer_orders = TransferOrder::whereIn('from_store_id', $user_stores)->where(['status', '=', 1])->latest()->paginate(20);

        return response()->json([
            'transfer_orders' => $transfer_orders
        ]);
    }

    public function my_store_delivers(){
        $user_stores = StoreUser::where('user_id', '=', auth('api')->id())->pluck('store_id');
        $transfer_orders = TransferOrder::whereIn('to_store_id', $user_stores)->where(['status', '=', 1])->latest()->paginate(20);

        return response()->json([
            'transfer_orders' => $transfer_orders
        ]);
    }

    public function reject(Request $request, $id)
    {
        $this->validate($request, [
            'note' => 'required',
        ]);

        $transfer_order = $this->inventory_transfer_order_cancel($request, $id);
        
        return response()->json([
            'transfer_order' => $transfer_order,
        ], status: (is_string($transfer_order) ? 500 : 200));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'sometimes', 
            'issuing_store_id' => 'required|numeric', 
            'requesting_store_id' => 'required|numeric', 
            'status' => 'sometimes|numeric',
        ]);

        $transfer_order = $this->inventory_transfer_order_create($request);
        return response()->json([
            'transfer_order' => $transfer_order,
        ], status: (is_string($transfer_order) ? 500: 200));
    }

    public function show($id)
    {
        return response()->json([
            'transfer_order' => $this->inventory_transfer_order_get_by($_GET['t'] ?? 'id', $id, true),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'description' => 'sometimes', 
            'issuing_store_id' => 'required|numeric', 
            'requesting_store_id' => 'required|numeric', 
            'status' => 'sometimes|numeric',
        ]);

        $transfer_order = $this->inventory_transfer_order_update($request, $id);
        
        return response()->json([
            'transfer_order' => $this->inventory_transfer_order_get_by('id', $id, true),
        ], status: (is_string($transfer_order) ? 500 : 200));
    }

    public function destroy($id)
    {
        $transfer_order = TransferOrder::where('id', '=', $id)->first();

        $transfer_order->status = 5;
        $transfer_order->updated_by = auth('api')->id();
        $transfer_order->deleted_by = auth('api')->id();
        
        $transfer_order->save();

        return response()->json([
            'transfer_order' =>  TransferOrder::where('id', $id)->with(['from', 'to', 'approver', 'transfer_order_items'])->first(),
            'transfer_orders' => TransferOrder::orderBy('status', 'ASC')->with(['from', 'to'])->paginate(10),
        ]);
    }
}
