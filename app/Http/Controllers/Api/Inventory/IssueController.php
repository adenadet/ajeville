<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\General\LogTrait;
use Illuminate\Http\Request;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Inventory\TransferOrderTrait;
use App\Models\Inventory\TransferOrder;
use App\Models\Inventory\TransferOrderItem;

class IssueController extends Controller
{
    use LogTrait, StoreTrait, TransferOrderTrait;
    public function index()
    {
        //
    }

    public function initials()
    {
        $reference_id = $_GET['ref_id'];
        $issue_type = $_GET['t'];

        //Determining the parameters to be done
        if ($issue_type == 1){
            //This is for transfer Order Requests
            $issue_request = is_integer($reference_id) ? $this->inventory_transfer_order_get_by('id', $reference_id, false) : $this->inventory_transfer_order_get_by('unique_id', $reference_id, false);
            $store_id = $issue_request->issuing_store_id;
            $items = $issue_request->items;
            //Get a list of all the item id in the request
            $items = TransferOrderItem::where('transfer_request_id', '=', $issue_request->id)->pluck('item_id');
        }

        return response()->json([
            'fulfillables' => $this->inventory_store_items_fulfillable($store_id, $items),
            'issue_request' => $issue_request,
        ]);

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'issue_type' => 'required',
            'issuing_store_id' => 'required|numeric',
            'items' => 'required|array',
            'reference_id' => 'required',
        ]);

        if ($request->issue_type == 'transfer_order'){

        }
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
        
    }
}
