<?php

namespace App\Http\Controllers\Api\EMR\Admission;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AdmissionTrait;
use App\Http\Traits\EMR\ConsultantTrait;
use App\Http\Traits\EMR\VisitTrait;
use App\Models\EMR\Consultation\SpecialtyDoctor;
use App\Models\User;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    use AdmissionTrait, ConsultantTrait, VisitTrait;

    public function admit(Request $request, $id){
        $request = $this->admission_request_admit($request, $id);

        return response()->json([
            'request' => $request
        ], is_string($request) ? 500 : 200);
    }

    public function confirm($id){
        $request = $this->admission_request_confirm($id);

        return response()->json([
            'request' => $request
        ], is_string($request) ? 500 : 200);
    }

    public function destroy(string $id){
        $request = $this->admission_request_deactivate($id);

        return response()->json([
            'request' => $request
        ], is_string($request) ? 404 : 200);
    }

    public function index(){
        $requests = $this->admission_request_get_all($_GET['type'], $_GET, true, true);

        return response()->json([
            'requests' => $requests,
            'wards' => $this->admission_ward_get_all('active', null, false, false)
        ], is_string($requests) ? 404 : 200);
    }

    public function initials(){
        $doctors = SpecialtyDoctor::pluck('doctor_id');
        $consultants = User::select('id', 'first_name', 'last_name')->whereIn('id', $doctors)->get(); 
        return response()->json([
            'consultants' => $consultants,
            'reasons' => $this->admission_reason_get_all('active', false, false), 
            'types' => $this->admission_type_get_all('active', false, false),
            'visits' => $this->emr_visit_get_all('active', $_GET, false, false),

        ], );
    }
    
    public function prechecks(Request $request, $id){
        $request = $this->admission_request_pre_admission_checks_create($request, $id);

        return response()->json([
            'request' => $request
        ], is_string($request) ? 500 : 201);
    }

    public function show($id){
        $request = $this->admission_request_get_by(null, $id, true);

        return response()->json([
            'request' => $request,
        ], is_string($request) ? 404 : 200);
    }
    
    public function store(Request $request)
    {
        $request = $this->admission_request_create($request->all());

        return response()->json([
            'request' => $request
        ], is_string($request) ? 500 : 201);;
    }

    public function update(Request $request, $id)
    {
        $request = $this->admission_request_update($request->all(), $id);

        return response()->json([
            'request' => $request
        ], is_string($request) ? 500 : 200);;
    }
}
