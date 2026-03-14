<?php

namespace App\Services\Inventory;

use App\Models\Sales\Order;
use App\Models\Finance\Income;
use App\Models\Inventory\OrderFulfillment;
use App\Models\Inventory\StoreItem;
use App\Models\Inventory\StoreItemBatch;
use App\Models\Sales\OrderApproval;
use App\Models\Sales\OrderItem;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderFulfillmentService
{
    public function fulfillOrderItem(OrderItem $orderItem)
    {
        return DB::transaction(function () use ($orderItem) {

            $remaining = $orderItem->quantity - $orderItem->quantity_fulfilled;

            $batches = StoreItemBatch::where('store_id', $orderItem->store_id)
                ->where('item_id', $orderItem->item_id)
                ->where('quantity_remaining', '>', 0)
                ->orderBy('expiry_date')
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($remaining <= 0) break;

                $deduct = min($batch->quantity_remaining, $remaining);

                app(StockService::class)
                    ->decreaseStock($batch, $deduct, OrderItem::class, $orderItem->id);

                $remaining -= $deduct;

                $orderItem->increment('quantity_fulfilled', $deduct);
            }

            if ($remaining == 0) {
                $orderItem->update(['status' => 'fulfilled']);
            } else {
                $orderItem->update(['status' => 'partial']);
            }
        });
    }
}
