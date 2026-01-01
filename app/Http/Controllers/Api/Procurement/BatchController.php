<?php

namespace App\Http\Controllers\Api\Procurement;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Procurement\PurchaseOrderTrait;
use App\Models\Procurement\Batch;
use App\Models\Procurement\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BatchController extends Controller
{
    use PurchaseOrderTrait, StoreTrait;
    
    public function confirm(Request $request, String $id)
    {
        $batch = $this->procurement_batches_confirm_single($request, $id);

        return response()->json([
            'batch' => $batch,
        ], is_string($batch)? 500 : 200);
    }

    public function destroy(String $id)
    {
        $batch = $this->procurement_batch_delete($id);

        return response()->json([
            'batch' => $batch,
        ], is_string($batch)? 500 : 200);
    }

    public function index()
    {
        $batches = $this->procurement_batches_get_all($_GET['status'] ?? 'active', $_GET ?? null, true, true, $_GET['page']);

        return response()->json([
            'batches' => $batches,
        ], is_string($batches)? 500 : 200);
    }

    public function show($id)
    {
        if (isset($_GET['t'])){
            if ($_GET['t'] == 'purchase_order'){
                $purchase_fulfillments = $this->procurement_batches_get_all('purchase_order', $id, true, false, null);
            }
            else if($_GET['t'] == 'fulfillment'){
                $purchase_fulfillments = $this->procurement_batches_get_all('fulfillment', $id, true, false, null);
            }

            return response()->json([
                'items' => $purchase_fulfillments,
            ], is_string($purchase_fulfillments)? 500 : 200);
        }
        else{
            $batch = $this->procurement_batches_get_by(null, $id, true);
            return response()->json([
                'batch' => $batch,
            ], is_string($batch)? 404 : 200);
        }    
    }

    public function store(Request $request)
    {
        $purchase_order = $this->procurement_batches_create($request);
        return response()->json([
            'purchase_order' => $purchase_order,       
        ], is_string($purchase_order)? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        //$purchase_order =
        if ($request['ref_type'] == 'purchase_order'){
            $this->procurement_batch_update($request, $id);
        }

        return response()->json([
            'purchase_order' => $this->procurement_purchase_order_get_by('id', $request['ref_id'], true),       
        ]);
    }
}
