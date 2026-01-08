<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\PatientTrait;
use App\Http\Traits\EMR\VisitTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use PatientTrait, VisitTrait;
    public function index()
    {
        return response()->json([
            'visits' => $this->emr_visit_get_all('active', null, true, true, null),
            'patients' => $this->emr_patient_get_all('active', null, false, false, null),
            'temporary_patients' => $this->emr_patient_get_all('temporary', null, false, false, null),
            'appointments' => $this->emr_appointment_get_all('today', null, true, true, null),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
