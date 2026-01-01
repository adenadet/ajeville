<?php

namespace App\Http\Controllers\Api\EMR\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Traits\Operations\FileTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Imports\SpecificDrugImport;
use Excel;
use Illuminate\Http\Request;
use Storage;

class DrugItemController extends Controller
{
    use FileTrait, PharmacyTrait;
    //use FileTrait, PriceListTrait, ServiceTypeTrait;
    public function destroy($id)
    {
        
    }

    public function import(Request $request)
    {
        $upload = $this->file_upload_by_type($request['uploaded_file'], 'xlsx', 'upload/drug_items', 1);
        $drug_items = Excel::import(new SpecificDrugImport(), $upload);

        Storage::delete($upload);
        unlink($upload);

        return response()->json([
            'drugs'         => $this->pharmacy_drugs_get_all_drug_names(),
            'drug_forms'    => $this->pharmacy_drug_forms_get_all_drug_names(),
            'drug_items'    => $this->pharmacy_items_get_all('drugs', null, null, false, $_GET['page'] ?? 1),
        ]);
    }
    
    public function index()
    {
        return response()->json([
            'drugs'         => $this->pharmacy_drugs_get_all_drug_names(),
            'drug_forms'    => $this->pharmacy_drug_forms_get_all_drug_names(),
            'drug_items'    => $this->pharmacy_items_get_all('drugs', null, null, false, $_GET['page'] ?? 1),
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
