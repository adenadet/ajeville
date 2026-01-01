<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Traits\Inventory\StoreTrait;
use App\Http\Traits\Inventory\TransferOrderTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use StoreTrait, TransferOrderTrait;
    public function index()
    {
        return response()->json([
            'my_stores'             =>  $this->inventory_store_user_get('my_stores', null, true, true, null),
            'soon_to_expire_items'  => $this->inventory_user_store_item_get('soon_to_expire_items', null, null, true, null),
            'expired_items'         => $this->inventory_user_store_item_get('expired_items', null, null, true, null),
            'pending_in'            => $this->inventory_transfer_order_get_all('status', ['status' => 1, 't' => 'in'], true, true, 1),
            'pending_out'           => $this->inventory_transfer_order_get_all('status', ['status' => 1, 't' => 'out'], true, true, 1),
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
