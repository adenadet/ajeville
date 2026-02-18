<?php 

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\Request;
use App\Models\EMR\Laboratory\RequestDetail;
use App\Models\EMR\Laboratory\Result as LaboratoryResult;
use App\Models\EMR\Laboratory\RequestItem;
use Exception;
use Illuminate\Support\Facades\DB;

class LaboratoryRequestService
{
    public function create(array $data){
        return DB::transaction(function () use ($data) {

            $request = Request::create([
                'visit_id' => $data['visit_id'],
                'patient_id' => $data['patient_id'],
                'consultation_id' => $data['consultation_id'],
                'transaction_id' => $data['transaction_id'],
                'branch_id' => $data['branch_id'],
                'ordered_by' => auth()->id(),
                'status' => Request::StatusBooked,
            ]);

            foreach ($data['services'] as $serviceId) {
                RequestItem::create([
                    'request_id' => $request->id,
                    'service_id' => $serviceId,
                    'status' => RequestItem::StatusPending
                ]);
            }

            return $request;
        });
    }

    public function accept(Request $request): void
    {
        DB::transaction(function () use ($request) {

            if ($request->status !== Request::StatusBooked) {
                throw new \Exception("Invalid state transition.");
            }

            foreach ($request->request_items as $detail) {

                $service = $detail->service;

                foreach ($service->analytes as $serviceAnalyte) {

                    LaboratoryResult::create([
                        'request_detail_id' => $detail->id,
                        'analyte_id' => $serviceAnalyte->analyte_id,
                        'status' => LaboratoryResult::StatusPending,
                    ]);
                }

                $detail->update([
                    'status' => RequestItem::StatusConfirmed
                ]);
            }

            $request->update([
                'status' => Request::StatusOngoing,
            ]);
        });
    }

    public function verify($request)
    {
        if ($request->results()->where('status', '!=', LaboratoryResult::StatusEntered)->exists()) {
            throw new Exception("All results must be entered before verification.");
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

