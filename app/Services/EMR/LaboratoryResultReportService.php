<?php

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\Result;
use Exception;

class LaboratoryResultReportService
{

    public function build($result_id)
    {

        $result = Result::with([
            'request.patient',
            'request.visit',
            'service',
            'latestVersion.values.analyte'
        ])->findOrFail($result_id);

        $version = $result->latestVersion;

        if (!$version) {
            throw new Exception("Result has no version.");
        }

        return [

            'result_id' => $result->id,

            'patient' => [
                'name' => $result->request->patient->full_name,
                'gender' => $result->request->patient->gender,
                'dob' => $result->request->patient->date_of_birth,
                'hospital_number' => $result->request->patient->hospital_number
            ],

            'visit' => [
                'visit_number' => $result->request->visit->visit_number ?? null,
                'requested_at' => $result->created_at
            ],

            'service' => [
                'name' => $result->service->name
            ],

            'version' => $version->version_number,

            'values' => $version->values->map(function ($value) {

                return [

                    'analyte' => $value->analyte->name,

                    'value' => $value->value,

                    'unit' => $value->unit,

                    'reference_range' => $this->formatReferenceRange($value->reference_range),

                    'flag' => $value->flag,

                    'comment' => $value->comment
                ];
            }),

            'status' => $result->status,

            'verified_by' => optional($result->verifiedBy)->name,

            'released_at' => $result->released_at
        ];
    }

    protected function formatReferenceRange($range)
    {

        if (!$range) {
            return null;
        }

        if (is_string($range)) {
            $range = json_decode($range, true);
        }

        if (!isset($range['low']) || !isset($range['high'])) {
            return null;
        }

        return "{$range['low']} - {$range['high']}";
    }


    public function html($result_id)
    {

        $data = $this->build($result_id);

        return view('emr.laboratory.report', [
            'report' => $data
        ])->render();
    }

}