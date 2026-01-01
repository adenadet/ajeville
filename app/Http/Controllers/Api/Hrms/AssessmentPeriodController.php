<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\AssessmentTrait;
use Illuminate\Http\Request;

class AssessmentPeriodController extends Controller
{
    use AssessmentTrait;
    public function destroy(string $id)
    {
        $period = $this->hrms_assessment_period_deactivate($id);

        return response()->json([
            'period' => $period
        ], is_string($period) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'periods' => $this->hrms_assessment_period_get_all($_GET['type'] ?? 'active', $_GET['query'] ?? null, true, true)
        ]);
    }

    public function initials()
    {
        return response()->json([
            'periods' => $this->hrms_assessment_period_get_all($_GET['type'] ?? 'active', $_GET['query'] ?? null, true, true)
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'period' => $this->hrms_assessment_period_get_by($_GET['type'] ?? 'active', $id, true)
        ]);
    }

    public function store(Request $request)
    {
        $period = $this->hrms_assessment_period_create($request);

        return response()->json([
            'period' => $period,
        ], is_string($period) ? 500 : 200);
    }

    public function update(Request $request, string $id)
    {
        $period = $this->hrms_assessment_period_update($request, $id);

        return response()->json([
            'period' => $period,
        ], is_string($period) ? 500 : 200);
    }
}
