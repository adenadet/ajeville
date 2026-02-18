<?php 

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\Request;
use App\Models\EMR\Laboratory\RequestDetail;
use App\Models\EMR\Laboratory\Result as LaboratoryResult;
use Illuminate\Support\Facades\DB;
class LaboratoryVerificationService
{
    public function verifyRequest($request)
    {
        if ($request->results()->where('status', '!=', LaboratoryResult::StatusEntered)->exists()) {
            throw new \Exception("All results must be entered before verification.");
        }

        foreach ($request->results as $result) {
            $result->update([
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'status' => LaboratoryResult::StatusVerified
            ]);
        }

        $request->update([
            'status' => Request::StatusConfirmed
        ]);
    }
}
