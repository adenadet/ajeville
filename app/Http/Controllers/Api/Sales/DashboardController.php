<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\Sales\OrderTrait;
use App\Models\Sales\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use CustomerTrait, ItemTrait, OrderTrait;
    public function index()
    {
        return response()->json([
            'customers' => $this->crm_customer_get_all('this_month', ['status' => 'active'], false, true, null),
            'delivery_notes' => $this->sales_goods_delivered_get_all('this_month', ['status' => 'delivered'], true, true, null),
            'items' => $this->inventory_item_get_all('all', null, true, true, null),
            'orders' => $this->sales_order_get_all('this_month', ['status' => 'all',], true, true, null),
            'returns' => $this->sales_return_get_all('this_month', ['status' => 'all',], false, true, null),
            'customer_new' => Order::select('customer_id')->groupBy('customer_id')->havingRaw('COUNT(*) = 1')->count(),
            'customer_repeat' => Order::select('customer_id')->groupBy('customer_id')->havingRaw('COUNT(*) > 1')->count(),
            'monthly_sales' => $this->sales_order_sales_trends('month', 10),

        ], 200);
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        
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
