<?php 

namespace App\Services\EMR;

use App\Models\EMR\VisitTransactionCoverage;
use App\Models\Insurance\Claim;
use App\Models\Insurance\ClaimItem;
use Illuminate\Support\Facades\DB;
use Exception;

class ClaimService
{
    private function generateClaimNumber(): string
    {
        return 'CLM-' . now()->format('Ymd') . '-' . strtoupper(uniqid());
    }

    public function approve(int $claimId, float $approvedAmount)
    {
        return DB::transaction(function () use ($claimId, $approvedAmount) {

            $claim = Claim::lockForUpdate()->findOrFail($claimId);

            if ($claim->status !== Claim::StatusSubmitted) {
                throw new Exception("Claim not in submitted state.");
            }

            $claim->update([
                'approved_amount' => $approvedAmount,
                'rejected_amount' => max(0, $claim->total_covered - $approvedAmount),
                'approved_at' => now(),
                'status' => $approvedAmount < $claim->total_covered ? Claim::StatusPartiallyApproved : Claim::StatusApproved,
            ]);

            return $claim;
        });
    }

    public function generateFromVisit(int $visitId)
    {
        return DB::transaction(function () use ($visitId) {

            $coverages = VisitTransactionCoverage::where('visit_id', $visitId)
                ->where('approval_status', VisitTransactionCoverage::ApprovalApproved)
                ->where('is_claimed', false)
                ->lockForUpdate()
                ->get();

            if ($coverages->isEmpty()) {
                throw new Exception("No approved coverages available for claim.");
            }

            // Ensure all belong to same provider + plan
            $providerId = $coverages->first()->provider_id;
            $planId     = $coverages->first()->plan_id;

            foreach ($coverages as $coverage) {
                if ($coverage->provider_id !== $providerId ||
                    $coverage->plan_id !== $planId) {
                    throw new Exception("Mixed provider/plan in same visit not allowed.");
                }
            }

            // Prevent duplicate claim
            $existing = Claim::where('visit_id', $visitId)
                ->where('provider_id', $providerId)
                ->where('plan_id', $planId)
                ->exists();

            if ($existing) {
                throw new Exception("Claim already generated for this visit.");
            }

            $claim = Claim::create([
                'provider_id' => $providerId,
                'plan_id' => $planId,
                'patient_id' => $coverages->first()->patient_id,
                'visit_id' => $visitId,
                'claim_number' => $this->generateClaimNumber(),
                'status' => Claim::StatusDraft,
                'total_billed' => 0,
                'total_covered' => 0,
                'total_patient_portion' => 0,
            ]);

            $totals = [
                'billed' => 0,
                'covered' => 0,
                'patient' => 0,
            ];

            foreach ($coverages as $coverage) {

                $transaction = $coverage->transaction;

                $item = ClaimItem::create([
                    'insurance_claim_id' => $claim->id,
                    'visit_transaction_id' => $transaction->id,
                    'visit_transaction_coverage_id' => $coverage->id,
                    'agreed_price' => $transaction->agreed_price,
                    'covered_amount' => $coverage->covered_amount,
                    'patient_portion' => $coverage->patient_payable,
                    'status' => Claim::StatusDraft,
                ]);

                $coverage->update([
                    'insurance_claim_item_id' => $item->id,
                    'is_claimed' => true,
                    'locked' => true, // prevent edits
                ]);

                $totals['billed']  += $transaction->amount;
                $totals['covered'] += $coverage->covered_amount;
                $totals['patient'] += $coverage->patient_payable;
            }

            $claim->update([
                'total_billed' => $totals['billed'],
                'total_covered' => $totals['covered'],
                'total_patient_portion' => $totals['patient'],
            ]);

            return $claim;
        });
    }

    public function recordPayment(int $claimId, float $amount){
        return DB::transaction(function () use ($claimId, $amount) {
            $claim = Claim::lockForUpdate()->findOrFail($claimId);
            $newPaid = $claim->paid_amount + $amount;
            $claim->update([
                'paid_amount' => $newPaid,
                'paid_at' => now(),
                'status' => $newPaid >= $claim->approved_amount ? Claim::StatusPaid : Claim::StatusPartiallyPaid,
            ]);
            return $claim;
        });
    }

    public function submit(int $claimId)
    {
        $claim = Claim::findOrFail($claimId);

        if ($claim->status !== Claim::StatusDraft) {
            throw new Exception("Only draft claims can be submitted.");
        }

        $claim->update([
            'status' => Claim::StatusSubmitted,
            'submitted_at' => now(),
        ]);

        return $claim;
    }
}