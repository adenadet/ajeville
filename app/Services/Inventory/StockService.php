<?php

namespace App\Services\Inventory;

use App\Models\Sales\Order;
use App\Models\Finance\Income;
use App\Models\Inventory\ItemMovement;
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

class StockService
{
    public function adjustStockFromStoreTrait(array $data)
    {
        return DB::transaction(function () use ($data) {

            $batch = StoreItemBatch::lockForUpdate()->findOrFail($data['batch_id']);
            $difference = $data['new_quantity'] - $batch->balance;
            $batch->update(['balance' => $data['new_quantity']]);

            ItemMovement::create([
                'store_item_batch_id' => $batch->id,
                'type' => 'adjustment',
                'quantity' => abs($difference),
                'referenceable_type' => $data['reference_type'] ?? null,
                'referenceable_id' => $data['reference_id'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $batch;
        });
    }

    public function decreaseStock($batch, int $qty, $referenceType, $referenceId, $type)
    {
        return DB::transaction(function () use ($batch, $qty, $referenceType, $referenceId, $type) {
            $batch = StoreItemBatch::where('id', $batch->id)->lockForUpdate()->first();
            if ($batch->balance < $qty) {throw new Exception('Insufficient Stock');}

            if ($type == 'issued'){$batch->issued += $qty;}
            else if ($type == 'sold'){$batch->sold += $qty;}
            else if ($type == 'transferred'){$batch->transferred += $qty;}

            $batch->balance -= $qty;
            $batch->save();

            ItemMovement::create([
                'store_item_batch_id' => $batch->id,
                'type' => $type,
                'quantity' => $qty,
                'referenceable_type' => $referenceType,
                'referenceable_id' => $referenceId,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $batch;
        });
    }

    public function decreaseStockFromStoreTrait(array $data)
    {
        return DB::transaction(function () use ($data) {
            $batch = StoreItemBatch::lockForUpdate()->findOrFail($data['batch_id']);

            if ($batch->balance < $data['quantity']) {throw new Exception("Insufficient stock.");}

            if ($data['type'] == 'issued'){$batch->issued += $data['quantity'];}
            else if ($data['type'] == 'sold'){$batch->sold += $data['quantity'];}
            else if ($data['type'] == 'transferred'){$batch->transferred += $data['quantity'];}

            $batch->balance -= $data['quantity'];
            $batch->save();

            ItemMovement::create([
                'store_item_batch_id' => $batch->id,
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'referenceable_type' => $data['referenceable_type'] ?? null,
                'referenceable_id' => $data['referenceable_id'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $batch;
        });
    }

    public function increaseStock($batch, int $qty, $referenceType, $referenceId, $type)
    {
        return DB::transaction(function () use ($batch, $qty, $referenceType, $referenceId, $type) {
            $batch->lockForUpdate();
            $batch->increment('received', $qty);
            $batch->increment('balance', $qty);

            ItemMovement::create([
                'store_item_batch_id' => $batch->id,
                'type' => $type,
                'quantity' => $qty,
                'referenceable_type' => $referenceType,
                'referenceable_id' => $referenceId,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
        });
    }

    public function increaseStockFromStoreTrait(array $data){
        return DB::transaction(function () use ($data) {
            $batch = StoreItemBatch::lockForUpdate()->findOrFail($data['batch_id']);
            $batch->increment('balance', $data['quantity']);
            $batch->increment('received', $data['quantity']);

            ItemMovement::create([
                'store_item_batch_id' => $batch->id,
                'type' => $data['type'] ?? 'received',
                'quantity' => $data['quantity'],
                'reference_type' => $data['referenceable_type'] ?? null,
                'reference_id' => $data['referenceable_id'] ?? null,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $batch;
        });
    }

}
