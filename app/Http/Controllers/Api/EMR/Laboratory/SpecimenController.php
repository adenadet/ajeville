<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\EMR\LaboratoryTrait;

class SpecimenController extends Controller
{
    use LaboratoryTrait;

    public function action(Request $request)
    {
        return response()->json([
            'specimen' => $this->emr_laboratory_specimen_action($request),
        ]);
    }

    public function index()
    {
        return response()->json([
            'specimens' => $this->emr_laboratory_specimen_get_all($_GET['status'] ?? 'pending', $_GET, true, true),
        ]);
    }

    public function initials($id){
        $request = $this->emr_laboratory_request_get_by(null, $id, true);

        return response()->json([
            'request' => $request,
            'bottles' => $this->emr_laboratory_bottles_get_all('active', null, false, false),
        ], is_string($request) ? 404 : 200);
    }

    public function store(Request $request){
        $specimen = $this->emr_laboratory_specimen_create($request);
        return response()->json([
            'specimen' => $specimen,
        ], is_string($specimen) ? 500 : 201);
    }

    public function show($id){
        //
    }

    public function update(Request $request, $id){
        $specimen = $this->emr_laboratory_specimen_update($request, $id);

        return response()->json([
            'specimen' => $specimen,
        ], is_string($specimen) ? 500 : 200);
    }

    public function destroy($id)
    {
        $specimen = $this->emr_laboratory_specimen_deactivate($id);

        return response()->json([
            'specimen' => $specimen,
        ], is_string($specimen) ? 404 : 200);
    }
}
