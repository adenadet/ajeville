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

class IssuanceService
{
    public function fulfillOrderItem($order_item, $store_item, $type){
        DB::beginTransaction();
        try{
            $fulfillments = [];
            $fulfilledQty = 0;
            $quantity = $order_item->total_quantity;
            /*$batches = StoreItemBatch::with('batch')
                        ->where('store_item_id', $store_item->id)
                        ->where('balance', '>', 0)
                        ->whereHas('batch', function ($q) {
                            $q->WhereNull('expiry_date')->orwhere('expiry_date', '>=', Carbon::today());
                        })
                        ->orderByRaw('(SELECT expiry_date FROM batches WHERE batches.id = store_item_batches.batch_id) ASC')
                        ->get();
            */
            $batches = StoreItemBatch::where('store_item_id', '=', $store_item->id)
                        ->where('balance', '>', 0)
                        ->orderByRaw('(SELECT expiry_date FROM procurement_batches WHERE procurement_batches.id = inventory_store_item_batches.batch_id) ASC')
                        ->get();

            foreach ($batches as $sib) {
                if ($quantity <= 0) break;

                $available = $sib->balance;
                // Create fulfillment
                $useQty = min($available, $quantity);
                $fulfillment = OrderFulfillment::create([
                    //'uuid'          => $this->sales_generate_unique_id('fulfillment'),
                    'type'          => $type,
                    'store_item_id' => $store_item->id,
                    'reference_id'  => $order_item->id,
                    'batch_id'      => $sib->id,
                    'quantity'      => $useQty,
                    'created_by'    => auth('api')->id() ?? Auth::id(),
                    'updated_by'    => auth('api')->id() ?? Auth::id(),
                ]);
                //echo $fulfillment->id;"\n";
                $fulfillments[] = $fulfillment;

                // Update StoreItemBatch
                $sib->balance -= $useQty;
                if($type == 'sold'){$sib->sold += $useQty;}
                else if ($type == 'issued'){$sib->issued += $useQty;}
                else if ($type == 'transferred'){$sib->transferred += $useQty;}

                $sib->save();

                // Reduce remaining
                $quantity -= $useQty;
                $fulfilledQty += $useQty; 
            }

            $order_item->fulfilled_quantity = $fulfilledQty;
            $order_item->save();
            
            DB::commit();
                //;
            return $fulfillments;
        }
        catch(Exception $e){
            DB::commit();
            return $e->getMessage();
        }
    }
}