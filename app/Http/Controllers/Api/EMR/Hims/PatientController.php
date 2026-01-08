<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
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
use App\Http\Traits\General\SettingsTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\UMS\UserTrait;
use App\Models\Operations\Branch;

class PatientController extends Controller
{
    use InsuranceTrait, ItemTrait, PatientTrait, SettingsTrait, TransactionTrait, UserTrait;

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
        $patient = $this->emr_patient_get_by_id(null, request()->cookie('current_patient'), true);
        return response()->json([
            'patient' => $patient,
        ], is_string($patient) ? 500 : 200);
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

    public function set_cookie(Request $request)
    {
        $patient = $request->input('patient');
        $cookie = cookie('current_patient', $patient['id'], 3600);
        return response('Cookie has been set')->cookie($cookie);
    }

    public function show($id)
    {
        return response()->json([
            'patient' => $this->emr_patient_get_by_id(null, $id, true),
            'transactions' => Transaction::where('patient_id', '=', $id)->latest()->paginate(10),     
        ]);
    }

    public function store(Request $request)
    {
        //Create New User
        $patient = $this->emr_patient_create($request);


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
