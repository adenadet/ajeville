<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\EMR\LaboratoryTrait;

class AnalyteController extends Controller
{
    use LaboratoryTrait;

    public function destroy($id)
    {
        $analyte = $this->emr_laboratory_analyte_deactivate($id);
        
        return response()->json([
            'analyte' => $analyte,
        ], is_string($analyte) ? 500 : 200);
    
    }

    public function index()
    {
        return response()->json([
            'analytes' => $this->emr_laboratory_analyte_get_all('active', $_GET, true, true),
        ]);
    }

    public function store(Request $request)
    {
        $analyte = $this->emr_laboratory_analyte_create($request);
        
        return response()->json([
            'analyte' => $analyte,
        ], is_string($analyte) ? 500 : 201);
    }

    public function show($id)
    {
        $analyte = $this->emr_laboratory_analyte_get_by($id, true);
        return response()->json([
            'analyte' => $analyte,
            'reference_ranges' => $this->emr_laboratory_reference_range_get_all('analyte', ['id' => $id], true, false),
        ], is_string($analyte) ? 404 : 200);
    
    }

    public function update(Request $request, $id)
    {
        $analyte = $this->emr_laboratory_analyte_update($request, $id);

        return response()->json([
            'analyte' => $analyte,
        ], is_string($analyte) ? 500 : 200);
    }

}
