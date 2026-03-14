<?php 

namespace App\Services\EMR;

use App\Models\EMR\VisitTransactionCoverage;
use App\Models\Insurance\Authorization;
use App\Models\Insurance\AuthRequest;
use App\Models\Insurance\Plan;
use App\Models\Insurance\PlanBranch;
use App\Models\Insurance\Provider;
use App\Models\Insurance\ProviderType;
use Exception;
use Illuminate\Support\Facades\DB;

class InsuranceAuthorizationService
{
    public function approve($authorizationId, $authCode, array $transactionIds)
    {
        DB::transaction(function() use ($authorizationId, $authCode, $transactionIds) {

            $auth = Authorization::findOrFail($authorizationId);

            if ($auth->status !== Authorization::StatusRequested) {
                throw new Exception('Authorization not in request state');
            }

            $auth->update([
                'auth_code' => $authCode,
                'status' => Authorization::StatusApproved,
                'approved_at' => now(),
            ]);

            foreach ($transactionIds as $transactionId) {
                $coverage = VisitTransactionCoverage::where('visit_transaction_id', $transactionId)->lockForUpdate()->firstOrFail();

                if (!$coverage->requires_authorization) {
                    continue;
                }

                $coverage->update([
                    'insurance_authorization_id' => $auth->id,
                    'covered_amount' => $coverage->transaction->agreed_price,
                    'patient_payable' => 0,
                    'approval_status' => VisitTransactionCoverage::ApprovalApproved,
                ]);
            }

        });
    }
}