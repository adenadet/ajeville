<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Procurement\PurchaseOrderTrait;
use App\Http\Traits\Procurement\WorkOrderTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use PurchaseOrderTrait, WorkOrderTrait;
    public function index()
    {
        return response()->json([
            'purchase_orders'   => $this->procurement_purchase_order_get_all('ongoing', null, true, true, $_GET['page'] ?? 1),
            'work_orders'       => $this->procurement_work_order_get_all('all',  0, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
