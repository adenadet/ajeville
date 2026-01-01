<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Patient\Contact as PatientContact;

use App\Models\EMR\Appointment;
use App\Models\EMR\Service;
use App\Models\Operations\Area;
use App\Models\Operations\State;
use App\Models\Operations\Country;
use App\Models\EMR\PatientInsurance;
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
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Http\Traits\UMS\UserTrait;

class PatientController extends Controller
{
    use InsuranceTrait, ItemTrait, PatientTrait, TransactionTrait, UserTrait;

    public function all()
    {
        return response()->json([
            'patients' => Patient::with(['user'])->orderBy('unique_id', 'DESC')->get(),     
        ]);
    }

    public function get_cookie()
    {
        $patient_id = request()->cookie('current_branch');
        return response()->json([
            'branch' => Patient::where('id', '=', $patient_id)->first(),
        ]);
    }

    public function index()
    {
        return response()->json([
            'nations' => Country::select('id', 'name')->orderBy('name', 'ASC')->get(), 
            'patients' => $this->emr_patient_get_all('all', null, true, true, $_GET['page'] ?? 1),     
        ]);
    }

    public function initials()
    {
        //$providers = Provider::where('status', '=', 1)->pluck('id'); 
       
        return response()->json([
            'areas' => Area::orderBy('name', 'ASC')->get(),
            'nations' => Country::select('id', 'name')->orderBy('name', 'ASC')->get(), 
            'plans' => $this->insurance_provider_plan_get_all('active', null, true, false, null),
            'providers' => $this->insurance_provider_get_all('active', null, true, false, null),
            'provider_types' => $this->insurance_provider_type_get_all('active', null, true, false, null),
            'registration_types' => $this->inventory_item_get_all_items('service_type', 5, false, false, null, null),
            'states' => State::orderBy('name', 'ASC')->with('areas')->get(),
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
            'patient' => $this->emr_patient_get_by_id($id),
            'transactions' => Transaction::where('patient_id', '=', $id)->latest()->paginate(10),     
        ]);
    }

    public function store(Request $request)
    {
        //Create New User
        $patient = $this->emr_patient_create_new_patient($request);
        if(is_string($patient)){
            return response()->json([
                'message' => 'Patient creation failed',
                'errors' => $patient,
            ], 500, );
        }
        else{
            return response()->json([
                'patient' => $patient,
                'message' => 'Patient created successfully',
            ]);
        }
    }
    
    public function update(Request $request, $id)
    {
    $patient = $this->emr_patient_update($request, $id);

        if ($patient){
            return response()->json([
                'patient' => $patient,
                'message' => 'Patient updated successfully',
            ]);
        }
        else{
            return response()->json([
                'message' => 'Patient update failed',
                'errors' => $patient,
            ], 500, );
        }
    }

    public function destroy($id)
    {
        //
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
            'insurances' => $this->emr_patient_insurance_get_all('patient_all', $id, true, false, null)
        ]);
    }
}
