<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\InsuranceTrait;
use Illuminate\Http\Request;
use App\Models\EMR\Visit;
use App\Models\Finance\Transaction;
use App\Models\Inventory\Item;
use App\Models\EMR\LaboratoryRequest;
use App\Models\EMR\RadiologyRequest;
use App\Models\EMR\Patient;
use App\Models\EMR\Patient\Insurance;
use App\Services\EMR\PatientInsuranceService;

class InsuranceController extends Controller
{
    use InsuranceTrait;
    public function index()
    {
        
    }

    public function initials()
    {
        return response()->json([
            'provider_types' => $this->insurance_provider_type_get_all('active', null, true, false, null),
            'providers' => $this->insurance_provider_get_all('active', null, true, false, null),
            'plans' => $this->insurance_provider_plan_get_all('active', null, false, false, null),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => ['numeric', 'required', 'exists:emr_patients,id'],
            'plan_id' => ['numeric', 'exists:hmo_provider_plans,id'],
        ]);
        $insurance_service = new PatientInsuranceService();
        $patient_insurance = $insurance_service->create($request->input('patient_id'), $request->input());

        return response()->json([
            'patient_insurance' => $patient_insurance,
        ]);
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'patient_id' => ['numeric', 'required', 'exists:emr_patients,id'],
            'plan_id' => ['numeric', 'exists:hmo_provider_plans,id'],
        ]);
        
        $insurance = Insurance::findOrFail($id);
        $insurance_service = new PatientInsuranceService();
        $patient_insurance = $insurance_service->update($insurance, $request->input('patient_id'), $request->input());

        return response()->json([
            'patient_insurance' => $patient_insurance,
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
