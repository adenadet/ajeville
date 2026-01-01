<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Traits\CRM\CustomerTrait;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Operations\BranchTrait;
use App\Http\Traits\Sales\OrderTrait;
use App\Models\Sales\OrderReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReturnController extends Controller
{
    use BranchTrait, CustomerTrait, OrderTrait, StoreTrait;
    public function destroy(string $id)
    {
        $order_return = $this->sales_return_delete($id);

        return response()->json(['return' => $order_return], is_string($order_return) ? 500 : 200);
    }

    public function index()
    {
        $order_returns = $this->sales_return_get_all($_GET['status'] ?? 'active', $_GET['search'] ?? null, true, true, $_GET['page'] ?? 1);
    
        return response()->json(['returns' => $order_returns]);
    }

    public function initials()
    {
        $branch_id = request()->cookie('current_branch') ?? auth('api')->user()->branch_id;
        
        return response()->json([
            'customers' => $this->crm_customer_get_all('active', null, false, false, null),
            'orders' => $this->sales_order_get_all('all', $_GET['query'] ?? null, false, false, null),
            'price_lists' => $this->operation_branch_price_list_get_all('branch', $_GET['branch_id'] ?? $branch_id, false, false, null),
            'stores' => $this->inventory_store_user_get('my_stores', null, false, false, null),
        ]);
    }

    public function show(string $id)
    {
        $order_return = $this->sales_return_get_by('id', $id, true);

        return response()->json([
            'return_order' => $order_return], 
            is_string($order_return) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'                       => 'nullable|integer|exists:crm_customers,id',
            'price_list_id'                     => 'nullable|integer|exists:finance_price_lists,id',
            'date'                              => 'required|date',
            'return_items'                      => 'required|array|min:1',
            'return_items.*.item_id'            => 'required|integer|exists:inventory_items,id',
            'return_items.*.quantity'           => 'required|numeric|min:1',
            'return_items.*.unit_price'         => 'required|numeric|min:0',
            'return_items.*.discount'           => 'nullable|numeric|min:0',
            'sales_order_id'                    => 'nullable|string|exists:procurement_sales_orders,unique_id',
        ]);

        $order_return = $this->sales_return_create($request);

        return response()->json([
            'return' => $order_return
        ], is_string($order_return) ? 500 :201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'customer_id'                       => 'nullable|integer|exists:crm_customers,id',
            'price_list_id'                     => 'nullable|integer|exists:finance_price_lists,id',
            'date'                              => 'required|date',
            'return_items'                      => 'required|array|min:1',
            'return_items.*.item_id'            => 'required|integer|exists:inventory_items,id',
            'return_items.*.quantity'           => 'required|numeric|min:1',
            'return_items.*.unit_price'         => 'required|numeric|min:0',
            'return_items.*.discount'           => 'nullable|numeric|min:0',
            'sales_order_id'                    => 'nullable|string|exists:procurement_sales_orders,unique_id',
        ]);

        $order_return = $this->sales_breturn_update($request, $id);

        return response()->json([
            'return' => $order_return
        ], is_string($order_return) ? 500 :200);
    }

}
