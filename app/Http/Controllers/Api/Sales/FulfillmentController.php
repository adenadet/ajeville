<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Sales\OrderTrait;
use Illuminate\Http\Request;

class FulfillmentController extends Controller
{
    use OrderTrait, StoreTrait;
    public function index()
    {
        $order = $this->sales_order_get_by('unique_id', $_GET['order_id'], true);
        $batches = $this->inventory_store_item_order_fulfillable($_GET['order_id']);

        return response()->json([
            'batches' => $batches,  
            'order' =>  $order,   
        ], is_string($order) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fulfillments' => 'required|array',
            'fulfillments.*.so_item_id' => 'required|exists:order_items,id',
            'fulfillments.*.batch_id' => 'required|exists:store_item_batches,id',
            'fulfillments.*.quantity' => 'required|numeric|min:1',
        ]);

        $order = $this->inventory_store_item_order_fulfillment($data);

        return response()->json(['order' => $order], is_string($order) ? 500 : 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
