<?php

namespace App\Services\EMR;

use App\Models\EMR\Visit;
use App\Models\EMR\VisitTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class VisitService
{
    public function end_check($visit_id)
    {

        $visit = Visit::with(['transactions'])->findOrFail($visit_id);
        $transactions = $visit->transactions()->whereNull('deleted_at')->get();
        $unpaid = $transactions->filter(function ($trx) {return $trx->service_status == 1 && $trx->status != VisitTransaction::StatusPaid;})->values();
        $pending = $transactions->filter(function ($trx) {return $trx->service_status != VisitTransaction::StatusCompleted && $trx->status != VisitTransaction::StatusCancelled && $trx->status != VisitTransaction::StatusDeferred;})->values();
        
        return [
            'visit' => $visit,
            'unpaid' => $unpaid,
            'pending' => $pending
        ];
    }

    public function end_visit($visit_id, $defer_items = [])
    {

        return DB::transaction(function () use ($visit_id, $defer_items) {

            $visit = Visit::with(['transactions'])->lockForUpdate()->findOrFail($visit_id);

            $unpaid = $visit->transactions()
                ->where('service_status', 1)
                ->where('status', '!=', VisitTransaction::StatusPaid)
                ->whereNull('deleted_at')
                ->get();

            if ($unpaid->count() > 0) {
                throw new Exception(
                    "Visit cannot be closed. There are unpaid performed services."
                );
            }

            $pendingTransactions = $visit->transactions()
                ->whereNull('deleted_at')
                ->whereNotIn('status', [
                    VisitTransaction::StatusCompleted,
                    VisitTransaction::StatusCancelled,
                    VisitTransaction::StatusDeferred
                ])
                ->get();


            foreach ($pendingTransactions as $trx) {
                if (in_array($trx->id, $defer_items)) {$trx->status = VisitTransaction::StatusDeferred;}
                else {$trx->status = VisitTransaction::StatusCancelled;}

                $trx->updated_by = auth('api')->id();
                $trx->updated_at = now();
                $trx->save();
            }

            $visit->status = Visit::StatusClosed;
            $visit->end_date = now()->format('Y-m-d');
            $visit->end_timestamp = now();
            $visit->updated_by = auth('api')->id();
            $visit->updated_at = now();

            $visit->save();

            return $visit->load(['patient', 'transactions']);
        });

    }

}