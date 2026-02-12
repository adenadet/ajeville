<?php

namespace App\Services\EMR;

use App\Models\EMR\VisitTransaction;
use App\Models\EMR\VisitPaymentAllocation;
use App\Models\EMR\VisitPayment;
use App\Models\EMR\VisitTransactionCoverage;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    public function reversePayment($paymentId)
    {
        return DB::transaction(function () use ($paymentId) {
            $payment = VisitPayment::with('allocations.visitTransaction')->findOrFail($paymentId);
            foreach ($payment->allocations as $allocation) {
                $transaction = $allocation->visitTransaction;
                $allocation->delete();
            }
            $payment->update(['status' => VisitPayment::StatusReversed, 'balance' => 0]);
            return $payment;
        });
    }

    public function reverseTransactionCharge($transactionId, $reason = null)
    {
        return DB::transaction(function () use ($transactionId, $reason) {

            $transaction = VisitTransaction::with(['coverage', 'paymentAllocations.visitPayment'])->findOrFail($transactionId);

            if ($transaction->status == VisitTransaction::StatusCancelled) {
                throw new Exception('Transaction already cancelled.');
            }

            foreach ($transaction->paymentAllocations as $allocation) {
                $payment = $allocation->visitPayment;
                if (!$payment) {continue;}

                // Restore balance back to payment
                $payment->balance += $allocation->amount;
                $payment->save();

                // Soft delete allocation
                $allocation->delete();
            }

            if ($transaction->coverage) {
                $transaction->coverage->update([
                    'claim_status' => VisitTransactionCoverage::ClaimRejected,
                    'approval_status' => VisitTransactionCoverage::ApprovalRejected,
                ]);
            }

            $transaction->update([
                'status' => VisitTransaction::StatusCancelled,
                'reversed_reason' => $reason,
                'reversed_at' => now(),
            ]);

            return $transaction->fresh();
        });
    }
}
