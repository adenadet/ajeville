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
        
    }

    public function show($id)
    {
        //
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
