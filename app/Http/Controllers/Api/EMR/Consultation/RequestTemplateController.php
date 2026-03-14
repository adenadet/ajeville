<?php

namespace App\Http\Controllers\Api\EMR\Consultation;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\ConsultationTrait;
use App\Models\EMR\Consultation\RequestTemplate;
use Illuminate\Http\Request;


class RequestTemplateController extends Controller
{
    use ConsultationTrait;
    
    public function destroy(string $id)
    {
        $template = $this->emr_consultation_request_template_deactivate($id);

        return response()->json([
            'request_template' => $template,
        ], is_string($template) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'request_templates' => $this->emr_consultation_get_all('mine', null, true, true, $_GET['page'] ?? 1), //$this->consultant_queue_mine(),
        ]);
    }

    public function show(string $id)
    {
        $template = $this->emr_consultation_request_template_get_by(null, $id, true);

        return response()->json([
            'request_template' => $template,
        ], is_string($template) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $template = $this->emr_consultation_request_template_create($request);

        return response()->json([
            'request_template' => $template,
        ], is_string($template) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $template = $this->emr_consultation_request_template_update($request, $id);

        return response()->json([
            'request_template' => $template,
        ], is_string($template) ? 500 : 200);
    }
}
