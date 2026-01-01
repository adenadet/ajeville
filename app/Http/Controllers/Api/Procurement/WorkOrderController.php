<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\General\SettingsTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Procurement\SettingsTrait as ProcurementSettingsTrait;
use App\Http\Traits\Procurement\VendorTrait;
use App\Http\Traits\Procurement\WorkOrderTrait;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    use ItemTrait, WorkOrderTrait, ProcurementSettingsTrait, SettingsTrait, StoreTrait, VendorTrait;

    public function assign_vendor(Request $request)
    {
        $quest = $this->procurement_work_order_assign_vendor($request);

        return response()->json([
            'work_order' => $this->procurement_work_order_get_by('id', $request['po_id'] , true),
        ]);
    }

    public function destroy(string $id)
    {
        return response()->json([
            'work_order' => $this->procurement_work_order_get_all('status', $_GET['status'] ?? 'ongoing', true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function index()
    {
        return response()->json([
            'work_orders' => $this->procurement_work_order_get_all('status', $_GET['status'] ?? 'all', true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->inventory_item_category_get_all('type', [1], false, false, null),
            'departments' => $this->general_settings_department_get_all('active', null, false, false, null),
            'payment_terms' => $this->procurement_settings_payment_term_get_all('active', null, false, false, null),
            'stores' => $this->inventory_store_get_all('active', null, false, false, null),
            'vendors' => $this->procurement_vendor_get_all('active', null, false, false, null),       
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'work_order' => $this->procurement_work_order_get_by('id', $id, true),
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'work_order' => $this->procurement_work_order_create($request),
        ]);
    }

    public function update(Request $request, string $id)
    {
        return response()->json([
            'work_order' => $this->procurement_work_order_update($request, $id),
        ]);
    }
}
