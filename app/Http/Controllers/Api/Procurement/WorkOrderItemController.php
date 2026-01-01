<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Procurement\WorkOrderTrait;
use App\Http\Traits\Procurement\SettingsTrait;
use Illuminate\Http\Request;
class WorkOrderItemController extends Controller
{
    use ItemTrait, SettingsTrait, WorkOrderTrait;
    public function destroy(string $id)
    {
        return response()->json([
            'work_order_item' => $this->procurement_work_order_item_delete($id),
        ]);
    }

    public function index()
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function store(Request $request)
    {
        return response()->json([
            'work_order_item' => $this->procurement_work_order_item_create($request),
        ]);
    }

    public function update(Request $request, string $id)
    {
        return response()->json([
            'work_order_item' => $this->procurement_work_order_item_update($request, $id),
        ]);
    }
}
