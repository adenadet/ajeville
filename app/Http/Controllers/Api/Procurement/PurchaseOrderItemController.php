<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\ItemTrait;
use Illuminate\Http\Request;

use App\Http\Traits\Procurement\PurchaseOrderTrait;
use App\Http\Traits\Procurement\SettingsTrait;
use App\Models\Procurement\PurchaseOrderItem;

class PurchaseOrderItemController extends Controller
{
    use ItemTrait, SettingsTrait, PurchaseOrderTrait;
    public function destroy(string $id)
    {
        return response()->json([
            'purchase_order_item' => $this->procurement_purchase_order_item_delete($id),
        ]);
    }

    public function index()
    {
        //
    }

    public function initials()
    {
        return response()->json([
            'items' => $this->inventory_item_get_all('active', null, false, false, null),
            'package_types' => $this->procurement_settings_package_type_get_all('active', null, false, false, null),
        ]);
    }

    public function show(string $id)
    {
        $purchase_order_items = PurchaseOrderItem::where('po_id', '=', $id)
            ->with(['item', 'package_type'])
            ->get();

        return response()->json([
            'purchase_order_items' => $purchase_order_items,
        ]);
    }

    public function store(Request $request)
    {
        $purchase_order_item = $this->procurement_purchase_order_item_create($request);

        return response()->json([
            'purchase_order_item' => $purchase_order_item,
        ], is_string($purchase_order_item) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        return response()->json([
            'purchase_order_item' => $this->procurement_purchase_order_item_update($request, $id),
        ]);
    }

    
}
