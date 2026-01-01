<?php

namespace App\Http\Controllers\Api\EMR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EMR\Appointment;
use App\Models\EMR\Patient;
use App\Models\EMR\RadFinding;
use App\Models\EMR\Schedule;
use App\Models\EMR\Service;
use App\Models\Area;
use App\Models\State;
use App\Models\Country;
use App\Models\User;

class CertificateController extends Controller
{
    public function index()
    {
        //
    }

    public function search(){
        if ($search = $_GET['q']){
            $patients = Patient::select('id')->orderBy('first_name', 'ASC')->where(function($query) use ($search){
                $query->where('first_name', 'LIKE', "%$search%")
                ->orWhere('middle_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%")
                ->orWhere('unique_id', 'LIKE', "%$search%");
                })->get();

            $consultations = Appointment::where('status', '=', 6)->whereDate('date', '<=', date('Y-m-d'))->whereIn('patient_id', $patients)->with(['front_officer', 'medical_officer', 'radiologist','service', 'patient.nationality', 'payment.employee', 'consent', 'consultation', 'report.findings', 'issuing_officer'])->orderBy('date', 'DESC')->paginate(30);
        }
        else{
            $consultations = Appointment::where('status', '=', 6)->whereDate('date', '<=', date('Y-m-d'))->whereNull(['front_office_id',])->with(['front_officer', 'medical_officer', 'radiologist','service', 'patient.nationality', 'payment.employee', 'consent', 'consultation', 'report.findings', 'issuing_officer'])->orderBy('date', 'DESC')->paginate(30);        
        }

        return response()->json([
            'appointments' => $consultations,
        ]);
    }


    public function show($id)
    {
        return response()->json([
            'appointment' => Appointment::where('id',$id)->with(['front_officer', 'medical_officer', 'radiologist','service', 'patient.nationality', 'payment.employee', 'consent', 'consultation', 'laboratory', 'report.findings', 'issuing_officer'])->first(), 
        ]);
    }

    public function store(Request $request)
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
