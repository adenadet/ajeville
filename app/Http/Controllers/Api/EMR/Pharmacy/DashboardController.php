<?php

namespace App\Http\Controllers\Api\EMR\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Models\EMR\Pharmacy\Prescription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use PharmacyTrait;
    
    public function destroy($id)
    {
        //
    }
    
    public function index()
    {
        return response()->json([
            'pending_prescriptions' => $this->emr_pharmacy_prescription_get_all('pending', null, true, true),
            'refill_prescriptions' => $this->emr_pharmacy_prescription_get_all('refillable', null, true, true),
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
