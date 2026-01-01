<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use App\Http\Traits\Sales\OrderTrait;
use Illuminate\Http\Request;

class GoodsDeliveredController extends Controller
{
    use OrderTrait;

    public function destroy(string $id)
    {
        //
    }

    public function index()
    {
        $delivery_notes = $this->sales_goods_delivered_get_all($_GET['type'] ?? 'all', $_GET['specific'] ?? null, true, true, null);

        return response()->json(['delivery_notes' => $delivery_notes,], is_string($delivery_notes) ? 500 : 200);
    }

    public function show(string $id)
    {
        $delivery_note = $this->sales_goods_delivered_get_by(null, $id, true);

        return response()->json(['delivery_note' => $delivery_note,], is_string($delivery_note) ? 500 : 200);
    }

    public function store(Request $request)
    {
        $delivery_note = $this->sales_goods_delivered_create($request);

        return response()->json(['delivery_note' => $delivery_note,], is_string($delivery_note) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
