<?php

namespace App\Http\Controllers\Api\Approvals;

use App\Http\Controllers\Controller;
use App\Http\Traits\Sales\OrderTrait;
use App\Models\Sales\Order;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    use OrderTrait;
    public function destroy(string $id)
    {
        $order = $this->sales_order_delete($id);

        return response()->json(['order' => $order,], is_string($order) ? 500 : 200);
    }
    public function index()
    {
        $orders = $this->sales_order_get_all($_GET['type'] ?? 'unapproved',  isset($_GET['query']) ? $_GET['query'] : null, true, true, $_GET['page'] ?? 1);

        return response()->json(['orders' => $orders,], is_string($orders) ? 500 : 200);
    }

    public function initials()
    {
        return response()->json([
            'customers' => $this->crm_customer_get_all('active', null, false, false, null),
            'package_types' => $this->procurement_settings_package_type_get_all('active', null, false, false, null),
            'payment_terms' => $this->procurement_settings_payment_term_get_all('active', null, false, false, null),
            'stores' => $this->inventory_store_user_get('my_stores', null, false, false, null),
        ]);
    }

    public function show(string $id)
    {
        $order = $this->sales_order_get_by('unique_id', $id, true);

        return response()->json(['order' => $order,], is_string($order) ? 404 : 200);
    }

    public function store(Request $request)
    {
        /*$order = $this->sales_order_approve($request, $id);

        return response()->json(['order' => $order,], is_string($order) ? 500 : 201);*/
    }

    public function update(Request $request, string $id)
    {
        $details = Order::find($id);
        //echo $details->status;
        if ($details->status == Order::StatusApproved){
            //echo $details->status;
            return response()->json(['order' => "Already approved",], 500);
        }
        $order = $this->sales_order_approve($request, $id);

        return response()->json(['order' => $order,], is_string($order) ? 500 : 201);
    }
}
