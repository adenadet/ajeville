<?php

namespace App\Http\Controllers\APi\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\LaboratoryTrait;
use Illuminate\Http\Request;

class ResultTemplateController extends Controller
{
    use LaboratoryTrait;

    public function destroy($id)
    {
        $result_template = $this->emr_laboratory_result_template_deactivate($id);
        return response()->json([
            'result_template' => $result_template,
        ], is_string($result_template) ? 400 : 200);
    }

    public function index()
    {
        return response()->json([
            'result_templates' => $this->emr_laboratory_result_template_get_all('active', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->emr_laboratory_category_get_all('active', null, false, false),
        ]);
    }

    public function show($id)
    {
        $result_template = $this->emr_laboratory_result_template_get_by(null, $id, true);
        return response()->json([
            'result_template' => $result_template,
        ], is_string($result_template) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $result_template = $this->emr_laboratory_result_template_create($request);
        return response()->json([
            'result_template' => $result_template,
        ], is_string($result_template) ? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        $result_template = $this->emr_laboratory_result_template_update($request, $id);

        return response()->json([
            'result_template' => $result_template,
        ], is_string($result_template) ? 500 : 200);
    }
}
