<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\EMR\Appointment;

class CertificateController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        if (!isset($_GET['p']) || !isset($_GET['f'])){
            return view('auth.certificate');
        }
        else if (!($_GET['p']) || !($_GET['f'])){
            return view('auth.certificate');
        }
        else{
            $appointment = Appointment::where('id',$id)->with(['front_officer', 'medical_officer', 'radiologist','service', 'patient.nationality', 'payment.employee', 'consent', 'consultation', 'laboratory', 'report.findings', 'issuing_officer'])->first();
        
            if ((strtolower($appointment->patient->passport_no) == strtolower($_GET['p'])) && (strtolower($appointment->patient->first_name) == strtolower($_GET['f']))){
                $params = ['appointment' => $appointment];
                return view('certificates.horizontal')->with($params);    
            }
            else{
                return view('errors.404');
            }
        }
    }

    public function edit($id)
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
