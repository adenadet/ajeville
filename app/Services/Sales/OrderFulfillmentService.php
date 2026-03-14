<?php 

namespace App\Services\Sales;

use App\Models\Inventory\StoreItemBatch;
use App\Models\Sales\OrderItem;
use App\Services\Inventory\StockService;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderFulfillmentService
{
    public function fulfillItem($orderItemId)
    {
        return DB::transaction(function () use ($orderItemId) {
            $orderItem = OrderItem::with(['sales_order'])->lockForUpdate()->findOrFail($orderItemId);
            $remaining = $orderItem->quantity - $orderItem->fulfilled_quantity;
            if ($remaining <= 0) { return $orderItem; }

            $batches = StoreItemBatch::whereHas('batch', function ($q) {
                    $q->whereDate('expiry_date', '>', now());
                })
                ->whereHas('store_item', function ($q) use ($orderItem) {
                    $q->where('store_id', $orderItem->sales_order->store_id)->where('item_id', $orderItem->item_id);
                })
                ->where('balance', '>', 0)->with('batch')
                ->join('procurement_batches', 'inventory_store_item_batches.batch_id', '=', 'procurement_batches.id')
                ->orderBy('procurement_batches.expiry_date', 'asc')
                ->select('inventory_store_item_batches.*')->lockForUpdate()->get();

            foreach ($batches as $storeBatch) {

                if ($remaining <= 0) break;
                $deduct = min($storeBatch->balance, $remaining);

                $stock_manager = new StockService();
                $stock_manager->decreaseStockFromStoreTrait([
                    'batch_id' => $storeBatch->id,
                    'quantity' => $deduct,
                    'referenceable_type' => OrderItem::class,
                    'referenceable_id' => $orderItem->id,
                ]);

                $remaining -= $deduct;
                $orderItem->increment('fulfilled_quantity', $deduct);
            }

            $orderItem->update(['status' => $remaining == 0 ? OrderItem::StatusFulfilled : OrderItem::StatusOngoing]);
            return $orderItem->fresh();
        });
    }

    public function fulfill_item_manually(array $data, $orderItemId)
    {
        return DB::transaction(function () use ($data, $orderItemId) {

            $orderItem = OrderItem::with(['sales_order'])->lockForUpdate()->findOrFail($orderItemId);
            $remaining = $orderItem->quantity - $orderItem->fulfilled_quantity;

            if ($remaining <= 0) {throw new Exception("Order item already fully fulfilled.");}

            if ($data['quantity'] > $remaining) {throw new Exception("Quantity exceeds remaining balance.");}

            $storeBatch = StoreItemBatch::where('id', $data['batch_id'])
                ->where('balance', '>', 0)
                ->whereHas('batch', function ($q) {$q->whereDate('expiry_date', '>', now());})
                ->lockForUpdate()->firstOrFail();

            if ($storeBatch->balance < $data['quantity']) {throw new Exception("Insufficient stock in selected batch.");}

            $store_manager = new StockService();
            $store_manager->decreaseStockFromStoreTrait([
                'batch_id' => $storeBatch->id,
                'store_id' => $orderItem->store_id,
                'quantity' => $data['quantity'],
                'reference_type' => $data['referenceable_type'],
                'reference_id' => $data['referenceable_id'],
            ]);

            $orderItem->increment('fulfilled_quantity', $data['quantity']);
            $newRemaining = $orderItem->quantity - $orderItem->fulfilled_quantity;

            $orderItem->update([
                'status' => $newRemaining == 0 ? OrderItem::StatusFulfilled : OrderItem::StatusOngoing,
            ]);

            return $orderItem->fresh();
        });
    }
}