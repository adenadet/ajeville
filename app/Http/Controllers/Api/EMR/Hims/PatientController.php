<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Services\EMR\MergePatientService;
use App\Services\EMR\PatientMergeService;
use Illuminate\Http\Request;

use App\Models\EMR\Patient;
use App\Models\EMR\PatientContact;

use App\Models\EMR\Appointment;
use App\Models\EMR\Service;
use App\Models\Operations\Area;
use App\Models\Operations\State;
use App\Models\Operations\Country;
use App\Models\EMR\Patient\Insurance as PatientInsurance;
use App\Models\Finance\PriceList;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\Transaction;
use App\Models\Insurance\Plan;
use App\Models\Insurance\Provider;
use App\Models\Insurance\ProviderType;
use App\Models\Inventory\Item;
use App\Models\NextOfKin;
use App\Models\User;

use App\Http\Traits\EMR\InsuranceTrait;
use App\Http\Traits\EMR\PatientTrait;
use App\Http\Traits\EMR\VisitTrait;
use App\Http\Traits\General\SettingsTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\UMS\UserTrait;
use App\Models\EMR\Patient\Patient as EMRPatient;
use App\Models\EMR\Visit;
use App\Models\EMR\VisitTransaction;
use App\Models\Operations\Branch;
use App\Services\EMR\PatientService;

class PatientController extends Controller
{
    use InsuranceTrait, ItemTrait, PatientTrait, SettingsTrait, TransactionTrait, UserTrait, VisitTrait;

    public function all()
    {
        return response()->json([
            'patients' => $this->emr_patient_get_all('all', null, false, false, null),     
        ]);
    }

    public function destroy($id)
    {
        //
    }

    public function get_cookie()
    {
        $patient_id = request()->cookie('current_patient');
        $patient = !empty($patient_id) ? $this->emr_patient_get_by_id(null, request()->cookie('current_patient'), true) : null;
        return response()->json([
            'patient' => $patient,
        ], is_null($patient) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'patients' => $this->emr_patient_get_all('all', null, true, true, $_GET['page'] ?? 1),     
        ]);
    }

    public function initials()
    {
        $providers = Provider::where('status', '=', 1)->pluck('id'); 
       
        return response()->json([
            'areas' => $this->general_settings_area_get_all('active', null, false, false, null),
            'nations' => $this->general_settings_country_get_all('active', null, false, false, null),
            'plans' => Plan::select('id', 'name', 'provider_id')->with('provider')->whereIn('provider_id', $providers)->orderBy('name', 'ASC')->get(),
            'providers' => $this->insurance_provider_get_all('active', null, true, false, null),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, true, false, null),
            'registration_types' => $this->inventory_item_get_all('classification_name', 'Registration', false, false, null),
            'states' => $this->general_settings_state_get_all('active', null, true, false, null),
        ]);
    }

    public function merge(Request $request)
    {
        $patient_merge = new PatientMergeService();
        $merge_patient = new MergePatientService();
        return response()->json([
            'patient' =>  $merge_patient->merge($request->input('source_id'), $request->input('target_id'), $request->input('reason'), ['keepTargetUser' => $request->input('keepTargetUser')])
        ]);
    }

    public function merge_preview()
    {
        return response()->json([
            'source' => $this->emr_patient_get_by_id(null, $_GET['source_id'], true),
            'target' => $this->emr_patient_get_by_id(null, $_GET['target_id'], true),
        ]);
    }

    public function search()
    {
        return response()->json([
            'patients' => $this->emr_patient_search_patients($_GET['q'])
        ]);
    }

    public function set_cookie(Request $request)
    {
        $patient = $request->input('patient');
        $cookie = cookie('current_patient', $patient['id'], 3600);
        return response('Cookie has been set '.$patient['id'])->cookie($cookie);
    }

    public function show($id)
    {
        $patient = $this->emr_patient_get_by_id(null, $id, true);
        if (is_string($patient)){
            return response()->json([
                'patient' => $patient,
            ], 404);    
        }
        //print $patient->id;
        $visit = $this->emr_visit_patient_active_visit($patient->id);
        $visit_id = is_string($visit) ? null : $visit->id;

        return response()->json([
            'patient' => $patient,
            'transactions' => $this->emr_visit_transaction_get_all(null, ['patient_id' => $patient->id, 'visit_id' => $visit_id ], true, true),
            'pending_transactions' => $this->emr_visit_transaction_get_all('pending', ['patient_id' => $patient->id, 'visit_id' => $visit_id], true, true), 
            'visit' => $visit,    
        ]);
    }

    public function store(Request $request)
    {
        //Create New User
        $patient_service = new PatientService();

        $patient = $request->input('reg_type') == EMRPatient::TypeReg ? $patient_service->createAndRegister($request) : $patient_service->createTemporary($request);

        return response()->json([
            'patient' => $patient,    
        ], is_string($patient) ? 500 : 201 );
    }
    
    public function update(Request $request, $id)
    {
        $patient = $this->emr_patient_update($request, $id);
        
        return response()->json([
            'patient' => $patient,     
        ]);
    }

    
    /*public function search()
    {
        if ($search = \Request::get('q')){
            $applicants = Patient::orderBy('first_name', 'ASC')->where(function($query) use ($search){
                $query->where('first_name', 'LIKE', "%$search%")
                ->orWhere('middle_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%");
                })->paginate(52);
            }
        else{
            $applicants = Patient::orderBy('first_name', 'ASC')->paginate(52);
        }
        
        return response()->json(['applicants' => $applicants,]);
    }*/

    public function insurances($id){
        return response()->json([
            'insurances' => PatientInsurance::where('patient_id', '=', $id)->with(['plan.provider', 'patient', ])->latest()->get(),     
        ]);
    }
}
