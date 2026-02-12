<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\LaboratoryTrait;
use Illuminate\Http\Request;

class ReferenceRangeController extends Controller
{
    use LaboratoryTrait;

    public function destroy($id)
    {
        $reference_range = $this->emr_laboratory_reference_range_deactivate( $id);

        return response()->json([
            'reference_range' => $reference_range,
        ], is_string($reference_range) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'reference_ranges' => $this->emr_laboratory_reference_range_get_all('active', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'analytes' => $this->emr_laboratory_analyte_get_all('active', null, false, false),
        ]);
    }

    public function show($id)
    {
        $reference_range = $this->emr_laboratory_reference_range_get_by(null, $id, true);
        return response()->json([
            'reference_range' => $reference_range,
            'referral_ranges' => $this->emr_laboratory_service_reference_range_get_all('reference_range', ['id' => $id], true, false),
        ], is_string($reference_range) ? 404 : 201);
    }

    public function store(Request $request)
    {
        $reference_range = $this->emr_laboratory_reference_range_create($request);
        return response()->json([
            'reference_range' => $reference_range,
        ], is_string($reference_range) ? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        $reference_range = $this->emr_laboratory_reference_range_update($request, $id);

        return response()->json([
            'reference_range' => $reference_range,
        ], is_string($reference_range) ? 500 : 200);
    }
}
