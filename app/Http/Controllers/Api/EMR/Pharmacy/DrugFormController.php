<?php

namespace App\Http\Controllers\Api\EMR\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\PharmacyTrait;
use Illuminate\Http\Request;

class DrugFormController extends Controller
{
    use PharmacyTrait;
    
    public function destroy($id)
    {
        $drug_form = $this->emr_pharmacy_drug_form_deactivate($id);

        return response()->json([
            'drug_form' => $drug_form,        
        ], is_string($drug_form) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'drug_forms' => $this->emr_pharmacy_drug_form_get_all($_GET['type'] ?? 'active', $_GET, true, true),        
        ]);
    }

    public function store(Request $request)
    {
        $drug_form = $this->emr_pharmacy_drug_form_create($request);

        return response()->json([
            'drug_form' => $drug_form,        
        ], is_string($drug_form) ? 500 : 201);
    }

    public function show($id)
    {
        $drug_form = $this->emr_pharmacy_drug_form_get_by($id, true);

        return response()->json([
            'drug_form' => $drug_form,        
        ], is_string($drug_form) ? 500 : 200);
    }

    public function update(Request $request, $id)
    {
        //
    }

}
