<?php

namespace App\Http\Controllers\Api\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\TransferOrderTrait;
use App\Http\Traits\Procurement\PurchaseOrderTrait;
use App\Http\Traits\Procurement\WorkOrderTrait;
use App\Http\Traits\Sales\OrderTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use OrderTrait, TransferOrderTrait, PurchaseOrderTrait, WorkOrderTrait;
    
    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        $sales_orders = $this->sales_order_get_all($_GET['type'] ?? 'unapproved',  isset($_GET['query']) ? $_GET['query'] : null, true, true, $_GET['page'] ?? 1);
        $transfer_orders = $this->inventory_transfer_order_get_all('unapproved', null, true, true, null);//['data' => [], 'total' => 0]; //Change this to 
        
        return response()->json([
            'report_purchase_order' => $this->inventory_transfer_order_summary_report('approvals', '2023-01-01', '2025-12-31', 'month', false),
            'report_sales_order' => $this->inventory_transfer_order_summary_report('approvals', '2023-01-01', '2025-12-31', 'month', false),
            'report_transfer_order' => $this->inventory_transfer_order_summary_report('approvals', '2023-01-01', '2025-12-31', 'month', false),
            'report_work_order' => $this->inventory_transfer_order_summary_report('approvals', '2023-01-01', '2025-12-31', 'month', false),
            'sales_orders' => $sales_orders,
            'transfer_orders' => $transfer_orders,
        ], is_string($sales_orders) ? 500 : 200);
    }
    
    public function show(string $id)
    {
        //
    }
    
    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
