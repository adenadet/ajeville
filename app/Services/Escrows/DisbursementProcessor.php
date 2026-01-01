<?php 
namespace App\Services\Escrows;

use App\Models\Escrows\Transaction;
use App\Models\Escrows\TransactionMileStone;
use App\Models\Escrows\Disbursement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DisbursementProcessor
{
    /**
     * Process paid transactions and create disbursements.
     * Adapt the paid-check to your schema (status, paid_at, etc.).
     */
    public function run()
    {
        $now = Carbon::now();

        // Query: adjust to your "paid" condition (status, paid_at, is_paid flag)
        $query = Transaction::where('status', 'paid')
            // make sure no disbursement exists yet
            ->whereDoesntHave('disbursement');

        // use chunkById for memory safety
        $query->chunkById(100, function ($txs) use ($now) {
            foreach ($txs as $tx) {
                try {
                    DB::transaction(function () use ($tx, $now) {
                        // For concurrency safety we re-query with a row lock
                        $fresh = Transaction::lockForUpdate()->find($tx->id);
                        if (! $fresh) return;

                        // double-check still paid and no disbursement
                        if ($fresh->status !== 'paid') return;
                        if ($fresh->disbursement()->exists()) return;

                        $amount = $fresh->amount; // change to your column
                        $fee = $fresh->fee ?? 0;   // adapt as needed
                        $net = $amount - $fee;

                        $disb = Disbursement::create([
                            'transaction_id' => $fresh->id,
                            'amount' => $net,
                            'fee' => $fee,
                            'status' => 'pending',
                            'scheduled_at' => $now,
                        ]);

                        // Optional: mark transaction so it won't be reprocessed
                        $fresh->update(['status' => 'disbursement_created']);
                    }, 5);
                } catch (\Throwable $e) {
                    Log::error("Disbursement creation failed for tx {$tx->id}: ".$e->getMessage());
                    // continue with next transaction
                }
            }
        });
    }
}
