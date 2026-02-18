<?php

namespace App\Services\EMR;

use App\Models\EMR\Radiology\Request as RadiologyRequest;
use App\Models\EMR\VisitTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RadiologyRequestService
{
    /*
    |--------------------------------------------------------------------------
    | 1. Start Request (Sample Collection)
    |--------------------------------------------------------------------------
    */
    public function start(RadiologyRequest $request, array $data): RadiologyRequest
    {
        if (!$request->transaction || $request->transaction->status !== VisitTransaction::StatusPaid) {
            throw ValidationException::withMessages([
                'transaction' => 'Transaction must be paid before starting radiology request.'
            ]);
        }

        return DB::transaction(function () use ($request, $data) {

            $request->update([
                'status'        => RadiologyRequest::StatusStarted,
                'sample_by'     => $data['sample_by'] ?? auth('api')->id(),
                'sample_at'     => now(),
                'sample_remark' => $data['sample_remark'] ?? null,
            ]);

            return $request->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Refer Request (Outsource)
    |--------------------------------------------------------------------------
    */
    public function refer(RadiologyRequest $request, array $data): RadiologyRequest
    {
        if ($request->status !== RadiologyRequest::StatusStarted) {
            throw ValidationException::withMessages([
                'status' => 'Only started requests can be referred.'
            ]);
        }

        return DB::transaction(function () use ($request, $data) {

            $request->update([
                'outsourced_by'        => $data['outsourced_by'] ?? auth('api')->id(),
                'outsourced_type'      => $data['outsource_type'],
                'outsourced_to_id'     => $data['outsourced_to_id'],
                'outsourced_status_id' => RadiologyRequest::StatusOutsourcePending,
                'outsourced_remark'    => $data['outsourced_remark'] ?? null,
                'status'               => RadiologyRequest::StatusReferredOut,
            ]);

            return $request->fresh();
        });
    }


    /*
    |--------------------------------------------------------------------------
    | 3. Submit Referred Result (Insourcing)
    |--------------------------------------------------------------------------
    */
    public function submitReferred(RadiologyRequest $request, array $data): RadiologyRequest
    {
        if ($request->status !== RadiologyRequest::StatusReferredOut) {
            throw ValidationException::withMessages([
                'status' => 'Only referred requests can be submitted.'
            ]);
        }

        return DB::transaction(function () use ($request, $data) {

            $request->update([
                'insourced_remark'        => $data['insourced_remark'] ?? null,
                'insourced_final_remark'  => $data['insourced_final_remark'],
                'outsource_result_file'   => $data['file'],
                'outsourced_status_id'    => RadiologyRequest::StatusOutsourceApproved,
                'status'                  => RadiologyRequest::StatusReported,
            ]);

            return $request->fresh();
        });
    }


    /*
    |--------------------------------------------------------------------------
    | 4. Create Report (Internal)
    |--------------------------------------------------------------------------
    */
    public function report(RadiologyRequest $request, array $data): RadiologyRequest
    {
        if ($request->status !== RadiologyRequest::StatusStarted) {
            throw ValidationException::withMessages([
                'status' => 'Only started requests can be reported.'
            ]);
        }

        return DB::transaction(function () use ($request, $data) {

            $request->update([
                'status'        => RadiologyRequest::StatusReported,
                'reported_by'   => $data['reported_by'] ?? auth('api')->id(),
                'reported_at'   => now(),
                'report_remark' => $data['report_remark'] ?? null,
            ]);

            return $request->fresh();
        });
    }


    /*
    |--------------------------------------------------------------------------
    | 5. Create Secondary Report
    |--------------------------------------------------------------------------
    */
    public function secondaryReport(RadiologyRequest $request, array $data): RadiologyRequest
    {
        if ($request->status !== RadiologyRequest::StatusSecondaryReport) {
            throw ValidationException::withMessages([
                'status' => 'Request is not awaiting secondary report.'
            ]);
        }

        return DB::transaction(function () use ($request, $data) {

            $request->update([
                'status'                     => RadiologyRequest::StatusSecondaryReport,
                'secondary_reported_by'      => $data['reported_by'] ?? auth('api')->id(),
                'secondary_reported_at'      => now(),
                'secondary_report_remark'    => $data['report_remark'] ?? null,
            ]);

            return $request->fresh();
        });
    }


    /*
    |--------------------------------------------------------------------------
    | 6. Confirm Report
    |--------------------------------------------------------------------------
    */
    public function confirm(RadiologyRequest $request, array $data): RadiologyRequest
    {
        if (!in_array($request->status, [
            RadiologyRequest::StatusReported,
            RadiologyRequest::StatusSecondaryReport
        ])) {
            throw ValidationException::withMessages([
                'status' => 'Only reported requests can be confirmed.'
            ]);
        }

        return DB::transaction(function () use ($request, $data) {

            $request->update([
                'status'          => RadiologyRequest::StatusConfirmed,
                'approved_by'     => $data['approved_by'] ?? auth('api')->id(),
                'approved_at'     => now(),
                'approval_remark' => $data['approval_remark'] ?? null,
            ]);

            return $request->fresh();
        });
    }
}
