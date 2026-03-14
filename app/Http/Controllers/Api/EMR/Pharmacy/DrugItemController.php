<?php

namespace App\Http\Controllers\Api\EMR\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Traits\Operations\FileTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Imports\SpecificDrugImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;

class DrugItemController extends Controller
{
    use FileTrait, PharmacyTrait;
    public function destroy($id)
    {
        
    }

    /*public function import(Request $request)
    {
        $upload = $this->file_upload_by_type($request['uploaded_file'], 'xlsx', 'upload/drug_items', 1);
        $drug_items = Excel::import(new SpecificDrugImport(), $upload);

        Storage::delete($upload);
        unlink($upload);

        return response()->json([
            'drugs'         => $this->emr_pharmacy_drugs_get_all_drug_names(),
            'drug_forms'    => $this->emr_pharmacy_drug_forms_get_all_drug_names(),
            'drug_items'    => $this->emr_pharmacy_drug_item_get_all('drugs', null, null, false, $_GET['page'] ?? 1),
        ]);
    }
    */
    
    public function index()
    {
        return response()->json([
            'drug_items'    => $this->emr_pharmacy_drug_item_get_all('drugs', null, null, false, $_GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        $drug_item = $this->emr_pharmacy_drug_item_create($request);

        return response()->json([
            'drug_items'    => $drug_item,
        ], is_string($drug_item) ? 500 : 201);
    }

    public function show($id)
    {
        $drug_item = $this->emr_pharmacy_drug_item_get_by(null, $id, true);

        return response()->json([
            'drug_items'    => $drug_item,
        ], is_string($drug_item) ? 404 : 200);
    }

    public function update(Request $request, $id)
    {
        $drug_item = $this->emr_pharmacy_drug_item_update($request, $id);

        return response()->json([
            'drug_items'    => $drug_item,
        ], is_string($drug_item) ? 500 : 200);
    }

}
