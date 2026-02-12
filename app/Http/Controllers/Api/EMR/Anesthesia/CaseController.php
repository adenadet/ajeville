<?php

namespace App\Http\Controllers\Api\EMR\Anesthesia;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AnesthesiaTrait;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    use AnesthesiaTrait;
    
    public function destroy(string $id)
    {
        $case = $this->emr_anesthesia_case_deactivate($id);

        return response()->json([
            'case' => $case,
        ], is_string($case) ? 500 : 200);
    }
    public function index()
    {
        return response()->json([
            'cases' => $this->emr_anesthesia_case_get_all($_GET['status'] ?? 'active', $_GET,true, true),
        ]); 
    }

    public function show(string $id)
    {
        $case = $this->emr_anesthesia_case_get_by('active', $id, true);
        
        return response()->json([
            'case' => $case,
        ], is_string($case) ? 500 : 200);
    }


    public function store(Request $request)
    {
        $case = $this->emr_anesthesia_case_create($request);
        return response()->json([
            'case' => $case,
        ], is_string($case) ? 500 : 200);
    }

    public function update(Request $request, string $id)
    {
        $case = $this->emr_anesthesia_case_update($request, $id);

        return response()->json([
            'case' => $case,
        ], is_string($case) ? 500 : 200);
    }    
}
