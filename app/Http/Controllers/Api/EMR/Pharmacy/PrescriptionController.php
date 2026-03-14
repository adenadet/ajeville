<?php

namespace App\Http\Controllers\Api\EMR\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Models\Branch;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Insurance;
use App\Models\EMR\Pharmacy\Prescription;
use App\Models\EMR\Pharmacy\PrescriptionDrug;
use App\Models\Finance\PriceList;
use Illuminate\Http\Request;

use App\Models\Finance\Transaction;
use App\Models\Inventory\Store;
use App\Models\Inventory\UserStore;

class PrescriptionController extends Controller
{
    use PharmacyTrait;

    public function confirm(Request $request, $id)
    {
        $prescription = Prescription::where('id', '=', $id)->first();

        foreach($request->input('drugs') as $drug){
            $transaction = Transaction::create([
                'date' => date('Y-m-d'),
                'visit_id' => $prescription->visit_id,
                'service_type_id' => 3,
                'patient_id' => $prescription->patient_id,
                'item_id' => $drug['specific_drug_id'],
                'qua'

            ]);
        }
        $prescription->status = 2;
        $prescription->updated_by = auth('api')->id();
        $prescription->save();

    }

    public function destroy($id)
    {
        $prescription = $this->emr_pharmacy_prescription_deactivate($id);

        return response()->json([
            'prescription' => $prescription,
        ], is_string($prescription) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'prescriptions' => $this->emr_pharmacy_prescription_get_all($_GET['type'] ?? 'pending', $_GET, true, true)
        ]);
    }

    public function show($id)
    {
        $prescription = $this->emr_pharmacy_prescription_get_by(null, $id, true);

        return response()->json([
            'prescription' => $prescription,
        ], is_string($prescription) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $prescription = $this->emr_pharmacy_prescription_create($request);

        return response()->json([
            'prescriptions' => $prescription,
        ], is_string($prescription) ? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        //
    }
}
