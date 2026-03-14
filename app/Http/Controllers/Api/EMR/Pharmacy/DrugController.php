<?php

namespace App\Http\Controllers\Api\EMR\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\PharmacyTrait;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    use PharmacyTrait;
    
    public function all(Request $request)
    {
        foreach ($request->input('drugs') as $drug){
            $data = [
                'name' => $drug['name'],
                'ham' => $drug['ham'] == 'Yes' ? 1 : 0,
                'status' => $drug['status'] == 'Active' ? 1 : 0,
            ];

            $this->pharmacy_drug_update($data, $drug['id']);
        }

        return response()->json([
            'drugs' => $this->emr_pharmacy_drug_get_all($_GET['type'] ?? 'excel', $_GET, true, false),        
        ]);
    }

    public function destroy($id)
    {
        $drug = $this->emr_pharmacy_drug_deactivate($id);

        return response()->json([
            'drug' => $drug,        
        ], is_string($drug) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'drugs' => $this->emr_pharmacy_drug_get_all($_GET['type'] ?? 'active', $_GET, true, true),        
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

}
