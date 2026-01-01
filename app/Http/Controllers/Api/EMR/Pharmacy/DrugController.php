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
            'drugs' => $this->pharmacy_drug_get_all('excel', null, true, false, null),        
        ]);
    }

    public function destroy($id)
    {
        //
    }

    public function index()
    {
        return response()->json([
            'drugs' => $this->pharmacy_drug_get_all('excel', null, true, false, null),        
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
