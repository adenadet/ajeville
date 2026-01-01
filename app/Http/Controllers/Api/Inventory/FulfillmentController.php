<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Inventory\TransferOrderTrait;
use App\Http\Traits\Sales\OrderTrait;
use Illuminate\Http\Request;

class FulfillmentController extends Controller
{
    use OrderTrait, StoreTrait, TransferOrderTrait;

    public function destroy(string $id)
    {
        //
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
        $request->validate([
            'type'                      => 'required|in:sold, transferred, issued',
            'reference_id'              => 'required|numeric',
            'item_id'                   => 'required|integer',
            'store_id'                  => 'required|integer',
            'fulfillments'              => 'required|array',
            'fulfillments.*.batch_id'   => 'required|integer',
            'fulfillments.*.quantity'   => 'required|numeric|min:0.01',
        ]);    

        foreach ($request->fulfillments as $f) {
            $res = $this->inventory_store_items_reduce_quantity($request->item_id, $request->store_id, $f['batch_id'], $request->type, $f['quantity'], $request->reference_id);

            if (is_string($res)) {
                return response()->json(['error' => $res], 500);
            }
        }
    }

    public function update(Request $request, string $id)
    {
        
    }
}
