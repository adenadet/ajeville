<?php 

namespace App\Services\EMR;

use App\Models\EMR\VisitTransaction;
use App\Models\EMR\VisitTransactionCoverage;
use App\Models\Insurance\Authorization;
use App\Models\Insurance\PlanBranch;
use Exception;
use Illuminate\Support\Facades\DB;

class InsuranceCoverageService
{
    private function findValidAuthorization($patientId, $providerId, $planId)
    {
        return Authorization::where('patient_id', $patientId)
            ->where('provider_id', $providerId)
            ->where('plan_id', $planId)
            ->where('status', Authorization::StatusApproved)
            ->where(function ($q) {
                $q->whereNull('valid_until')
                ->orWhere('valid_until', '>=', now());
            })
            ->latest()
            ->first();
    }

    public function applyAuthorization($authorization, array $transactionIds)
    {
        return DB::transaction(function () use ($authorization, $transactionIds) {

            if ($authorization->status !== $authorization::STATUS_APPROVED) {
                throw new Exception("Authorization is not approved.");
            }

            foreach ($transactionIds as $transactionId) {

                $coverage = VisitTransactionCoverage::where('visit_transaction_id', $transactionId)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$coverage->requires_authorization) {
                    continue;
                }

                if ($coverage->is_claimed || $coverage->locked) {
                    throw new Exception("Cannot modify claimed coverage.");
                }

                $coveredAmount = $coverage->agreed_price;

                $coverage->update([
                    'insurance_authorization_id' => $authorization->id,
                    'covered_amount' => $coveredAmount,
                    'patient_payable' => max(0, $coverage->agreed_price - $coveredAmount),
                    'approval_status' => VisitTransactionCoverage::ApprovalApproved,
                ]);
            }

            return true;
        });
    }

    public function approveCoverageWithAuthorization(VisitTransactionCoverage $coverage, Authorization $authorization)
    {
        return DB::transaction(function () use ($coverage, $authorization) {

            if ($coverage->approval_status !== VisitTransactionCoverage::ApprovalPending) {return;}

            if ($coverage->locked || $coverage->is_claimed) {throw new Exception("Coverage locked.");}

            $coverage->update([
                'insurance_authorization_id' => $authorization->id,
                'covered_amount' => $coverage->agreed_price,
                'patient_payable' => 0,
                'approval_status' => VisitTransactionCoverage::ApprovalApproved,
            ]);
        });
    }

    public function createForTransaction(VisitTransaction $transaction)
    {
        return DB::transaction(function () use ($transaction) {

            if ($transaction->coverage) {
                return $transaction->coverage;
            }

            $policy = $transaction->visit->patient->primaryInsurance();

            if (!$policy) {
                return null;
            }

            $planBranch = PlanBranch::where('plan_id', $policy->plan_id)
                ->where('branch_id', $transaction->branch_id)
                ->firstOrFail();

            $priceItem = $planBranch->priceListItems()
                ->where('service_id', $transaction->service_id)
                ->firstOrFail();

            $requiresAuth = $priceItem->requires_authorization;
            $agreedPrice  = $priceItem->agreed_price;

            // Check if valid authorization already exists
            $authorization = null;

            if ($requiresAuth) {
                $authorization = $this->findValidAuthorization(
                    $transaction->visit->patient_id,
                    $policy->provider_id,
                    $policy->plan_id
                );
            }

            $isApproved = !$requiresAuth || ($authorization !== null);

            $coveredAmount = $isApproved ? $agreedPrice : 0;

            $patientPayable = max(
                0,
                $transaction->amount - $coveredAmount
            );

            return VisitTransactionCoverage::create([
                'visit_transaction_id' => $transaction->id,
                'visit_id' => $transaction->visit_id,
                'provider_id' => $policy->provider_id,
                'plan_id' => $policy->plan_id,
                'patient_id' => $transaction->visit->patient_id,
                'agreed_price' => $agreedPrice,
                'covered_amount' => $coveredAmount,
                'patient_payable' => $patientPayable,
                'requires_authorization' => $requiresAuth,
                'insurance_authorization_id' => $authorization?->id,
                'approval_status' => $isApproved ? VisitTransactionCoverage::ApprovalApproved : VisitTransactionCoverage::ApprovalPending,
            ]);
        });
    }

    public function recalculate(VisitTransactionCoverage $coverage)
    {
        if ($coverage->locked || $coverage->is_claimed) {
            throw new Exception("Coverage is locked.");
        }

        $transaction = $coverage->transaction;

        $coveredAmount = $coverage->requires_authorization
            ? 0
            : $coverage->agreed_price;

        $coverage->update([
            'covered_amount' => $coveredAmount,
            'patient_payable' => max(0, $transaction->amount - $coveredAmount),
        ]);
    }

    public function rejectCoverage(VisitTransactionCoverage $coverage)
    {
        if ($coverage->is_claimed || $coverage->locked) {
            throw new Exception("Cannot reject claimed coverage.");
        }

        $coverage->update([
            'approval_status' => VisitTransactionCoverage::ApprovalRejected,
            'covered_amount' => 0,
            'patient_payable' => $coverage->agreed_price,
        ]);
    }

}