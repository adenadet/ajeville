<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\AssessmentTrait;
use Illuminate\Http\Request;

class AssessmentHrItemController extends Controller
{
    use AssessmentTrait;

    public function destroy(string $id)
    {
        $hr_item = $this->hrms_assessment_hr_item_deactivate($id);

        return response()->json([
            'hr_item' => $hr_item,
        ], is_string($hr_item) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'hr_items' => $this->hrms_assessment_hr_item_get_all($_GET['type'] ?? 'active', $_GET['query'] ?? null, true, true),
        ]);
    }

    public function show(string $id)
    {
        $hr_item = $this->hrms_assessment_hr_item_get_by(null, $id, true);

        return response()->json([
            'hr_item' => $hr_item,
        ], is_string($hr_item) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required', 
            'description' => 'sometimes|string', 
            'max_score' => 'required|numeric',
            'status' => 'sometimes|numeric',
        ]);

        $hr_item = $this->hrms_assessment_hr_item_create($request);

        return response()->json([
            'hr_item' => $hr_item,
        ], is_string($hr_item) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $hr_item = $this->hrms_assessment_hr_item_update($request, $id);

        return response()->json([
            'hr_item' => $hr_item,
        ], is_string($hr_item) ? 500 : 200);
    }
}
