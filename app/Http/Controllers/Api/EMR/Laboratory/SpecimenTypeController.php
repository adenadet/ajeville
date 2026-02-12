<?php

namespace App\Http\Controllers\APi\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\LaboratoryTrait;
use Illuminate\Http\Request;

class SpecimenTypeController extends Controller
{
    use LaboratoryTrait;

    public function index()
    {
        return response()->json([
            'specimen_types' => $this->emr_laboratory_specimen_type_get_all('active', $_GET, true, true),
        ]);
    }

    public function store(Request $request)
    {
        $specimen_type = $this->emr_laboratory_specimen_type_create($request);
        return response()->json([
            'specimen_type' => $specimen_type,
        ], is_string($specimen_type) ? 500 : 201);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $specimen_type = $this->emr_laboratory_specimen_type_update($request, $id);

        return response()->json([
            'specimen_type' => $specimen_type,
        ], is_string($specimen_type) ? 500 : 200);
    }

    public function destroy($id)
    {
        $specimen_type = $this->emr_laboratory_specimen_type_deactivate($id);

        return response()->json([
            'specimen_type' => $specimen_type,
        ], is_string($specimen_type) ? 404 : 200);
    }
}
