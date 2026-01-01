<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Procurement\PurchaseOrderTrait;
use App\Http\Traits\Procurement\SettingsTrait;
use App\Http\Traits\Procurement\VendorTrait;
use App\Models\Procurement\PurchaseOrder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    use ItemTrait, PurchaseOrderTrait, SettingsTrait, StoreTrait, VendorTrait;

    public function additional_costs(Request $request, $id)
    {
        $quest = $this->procurement_purchase_order_additional_cost($request, $id);

        return response()->json([
            'purchase_order' => $this->procurement_purchase_order_get_by('id', $id, true),
        ]);
    }

    public function approve(Request $request, $id)
    {
        $quest = $this->procurement_purchase_order_approve($request, $id);

        return response()->json([
            'message' => $quest,
            'purchase_order' => $this->procurement_purchase_order_get_by('id', $id , true),
        ], is_string($quest) ? 500 : 200);
    }

    public function assign_store(Request $request)
    {
        $quest = $this->procurement_purchase_order_assign('store', $request);

        return response()->json([
            'purchase_order' => $this->procurement_purchase_order_get_by('unique_id', $request['po_id'] , true),
        ], is_string($quest) ? 500 : 200);
    }

    public function assign_vendor(Request $request)
    {
        $quest = $this->procurement_purchase_order_assign('vendor', $request);

        return response()->json([
            'purchase_order' => $this->procurement_purchase_order_get_by('unique_id', $request['po_id'] , true),
        ]);
    }

    public function destroy(string $id)
    {
        $purchase_order = $this->procurement_purchase_order_delete($id);

        return response()->json([
            'purchase_order' => $purchase_order,
        ], is_string($purchase_order) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'purchase_orders' => $this->procurement_purchase_order_get_all($_GET['game'] ?? 'mine', $_GET ?? null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->inventory_item_category_get_all('type', [1], false, false, null),
            'package_types' => $this->procurement_settings_package_type_get_all('active', null, false, false, null),
            'payment_terms' => $this->procurement_settings_payment_term_get_all('active', null, false, false, null),
            'stores' => $this->inventory_store_get_all('active', null, false, false, null),
            'vendors' => $this->procurement_vendor_get_all('active', null, false, false, null),       
        ]);
    }

    public function initiate(){
        $purchase_order = $this->procurement_purchase_order_initiate();

        return response()->json([
            'purchase_order' => $this->procurement_purchase_order_get_by('id', $purchase_order->id, true),
        ], is_string($purchase_order) ? 500 : 200);
    }
    
    public function show(string $id)
    {
        return response()->json([
            'purchase_order' => $this->procurement_purchase_order_get_by('unique_id', $id, true),
        ]);
    }

    public function store(Request $request)
    {
        $purchase_order = $this->procurement_purchase_order_create($request);
        return response()->json([
            'purchase_order' => $purchase_order,
        ], is_string($purchase_order) ? 500 : 201);
    }

    public function submit($id)
    {
        try{
            $purchase_order = PurchaseOrder::findOrFail($id);

            if ($purchase_order->store_id == null || $purchase_order->vendor_id == null){
                throw new Exception("Purchase Order is missing required fields.");
            }

            if ($purchase_order->date == null){
                $purchase_order->date = date('Y-m-d');    
            }

            if($purchase_order->delivery_date == null){
                $purchase_order->delivery_date = date('Y-m-d', strtotime('+90 days'));
            }
            
            $purchase_order->status = 1;
            $purchase_order->updated_by = Auth::id() ?? auth('api')->id();
            $purchase_order->save();
        }
        catch(Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ], 404);
        }
        
        return response()->json([
            'purchase_order' => $this->procurement_purchase_order_get_by('id', $id, true),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $po = $this->procurement_purchase_order_update($request, $id);
        return response()->json([
            'purchase_order' => $po
        ], is_string($po) ? 500 : 200);
    }
}
