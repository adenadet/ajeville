<?php 

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\Request;
use App\Models\EMR\Laboratory\RequestDetail;
use App\Models\EMR\Laboratory\LaboratoryResult;
use App\Models\EMR\Laboratory\OutsourceOrder;
use App\Models\EMR\Laboratory\OutsourceOrderItem;
use App\Models\EMR\Laboratory\RequestItem;
use Exception;
use Illuminate\Support\Facades\DB;

class LaboratoryOutsourceService
{
    public function create(Request $request, array $details, array $data)
    {
        return DB::transaction(function () use ($request, $details, $data) {

            $order = OutsourceOrder::create([
                'request_id' => $request->id,
                'branch_id' => $request->branch_id,
                'outsource_type' => $data['type'], // intra_branch | vendor
                'target_branch_id' => $data['target_branch_id'] ?? null,
                'vendor_id' => $data['vendor_id'] ?? null,
                'status' => 0,
                'remark' => $data['remark'] ?? null,
            ]);

            foreach ($details as $detail) {
                OutsourceOrderItem::create([
                    'outsource_order_id' => $order->id,
                    'request_detail_id' => $detail->id,
                    'status' => 0,
                ]);

                $detail->update([
                    'status' => RequestItem::StatusOutsourced
                ]);
            }

            return $order;
        });
    }

    public function send($order)
    {
        if ($order->status !== 0) {
            throw new Exception("Invalid state.");
        }

        $order->update([
            'status' => 10,
            'sent_by' => auth()->id(),
            'sent_at' => now()
        ]);
    }
}