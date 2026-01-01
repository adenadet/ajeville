<?php

namespace App\Http\Controllers\Api\EMR\Pharmacy;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\EMR\Patient;
use App\Models\EMR\PatientInsurance;
use App\Models\EMR\Prescription;
use App\Models\EMR\PrescriptionDrug;
use App\Models\Finance\PriceList;
use Illuminate\Http\Request;

use App\Models\Finance\Transaction;
use App\Models\Inventory\Store;
use App\Models\Inventory\UserStore;

class PrescriptionController extends Controller
{
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

    public function index()
    {
        return response()->json([
            'prescriptions' => Prescription::where('status', '=', 1)->with(['doctor.user', 'patient.user'])->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $branch_id = request()->cookie('current_branch');
        $prescription = Prescription::where('id', '=', $id)->select('id', 'visit_id', 'doctor_id', 'doctor_name', 'consultation_id', 'patient_id', 'updated_at')->with(['doctor'])->first();
        $insurances = PatientInsurance::where('patient_id', '=', $prescription->patient_id)->pluck('plan_id');
        $user_stores = UserStore::where('user_id', '=', auth('api')->id())->pluck('store_id');
        $issuing_stores = Store::where('branch_id', '=', $branch_id)->whereIn('id', $user_stores)->get(); 
        $price_lists = PriceList::select('id', 'name', 'type_id')->where('branch_id', '=', $branch_id)->where(
            function($query) use ($insurances){
                return $query->whereIn('plan_id', $insurances)->orWhere('type_id', '=', 0); 
            }
        )->with(['price_list_items'])->orderBy('type_id', 'DESC')->get();
        
        //where(function($query) use ($insurances){return $query->where('type_id', '=', 0)->whereIn('plan_id', $insurances);})->
        

        $drugs = PrescriptionDrug::where('prescription_id', '=', $id)->with(['drug.specific_drugs', 'specific_drug', 'specifics'])->get();

        return response()->json([
            'drugs' => $drugs,
            'prescription' => $prescription,
            'issuing_stores' => $issuing_stores,
            'price_lists' => $price_lists,
            'branch' => Branch::where('id', '=', $branch_id)->first(),
            'insurances' => $insurances,
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
