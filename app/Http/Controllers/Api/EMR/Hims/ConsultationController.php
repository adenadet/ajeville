<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\ConsultationTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\Operations\Branch;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Drugs\Form as DrugForm;
use App\Models\EMR\Drugs\Route as DrugRoute;
//use App\Models\EMR\Frequency;
use App\Models\EMR\ServiceType;
use App\Models\EMR\Consultation\Specialty;
use App\Models\EMR\Symptom;
use App\Models\EMR\Visit;
use App\Models\EMR\Settings\Duration;
use App\Models\EMR\Settings\ICD10;
use App\Models\EMR\Settings\Location;
use App\Models\EMR\Settings\Position;
use App\Models\Finance\Transaction;
use App\Models\HRMS\Employee;
use App\Models\Inventory\Item;
use App\Models\User;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    use ConsultationTrait, ItemTrait;

    public function begins()
    {
        return response()->json([
            'groups' => [],
            'services' => $this->inventory_item_get_all('service_type', 4, false, false, null, null),
            'specialties' => Specialty::select('id', 'name')->with('doctors')->orderBy('name', 'ASC')->get(),
        ]); 
    }

    public function destroy($id)
    {
        //
    }
    
    public function index()
    {
        return response()->json([
            'consultations' => Consultation::whereIn('status', [0, 1])->with([])->latest()->paginate(50)
        ]);
    }

    public function initials()
    {
        return response()->json([
            'durations' => Duration::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'drug_forms' => DrugForm::select('name')->orderBy('name', 'ASC')->get(),
            //'frequencies' => Frequency::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'icd_10_codes' => ICD10::select('id', 'code', 'name')->orderBy('code', 'ASC')->get(),
            //'laboratory_services' => $this->inventory_item_get_all('service_type_name', 'Laboratory Investigations', false, false, null, null),
            'locations' => Location::select('name')->orderBy('name', 'ASC')->get(),
            'positions' => Position::select('name')->orderBy('name', 'ASC')->get(),
            //'radiology_services' => $this->inventory_item_get_all('service_type_name', 'Radiology Investigations', false, false, null, null),
            'routes' => DrugRoute::select('name')->orderBy('name', 'ASC')->get(),
            'specific_drugs' => $this->inventory_item_get_all('classification_name', 'Drugs', false, false, null),
            //'symptoms' => Symptom::select('id', 'name', 'code', 'group_code')->orderBy('name', 'ASC')->get(),
        ]); 
    }

    public function show($id)
    {
        
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'consultation_type_id'          => 'required|numeric', 
            'patient_id'                    => 'required|numeric',
            'visit_id'                      => 'required|numeric',
            'specialty_id'                  => 'required|numeric',
            'whom_to_see'                   => 'required|string',
            //'consultant_id'                 => 'required|numeric',
        ]);
        //$consultation = $this->create_consultation_transaction($request->input('consultation_type_id'), $request->input('patient_id'), 1, false, $request->input('visit_id'), $request->input('specialty_id'), $request->input('whom_to_see'), $request->input('consultant_id'));

        return response()->json([
            'message' => 'Successful',
            'consultation' => $this->create_consultation_transaction($request),
        ]); 
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function visit_initials($id)
    {
        return response()->json([
            'durations' => Duration::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'services' => Item::where('service_id', '=', 1)->where('status', '=', 1)->get(),
            'specialties' => Specialty::orderBy('name', 'ASC')->get(),
            //'symptoms' => Symptom::select('id', 'name', 'code', 'group_code')->orderBy('name', 'ASC')->get(), 
            'visit' => Visit::where('unique_id', '=', $id)->with(['branch', 'patient.user', 'visit_type'])->first(),
        ]); 
    }

    public function visit($id)
    {
        $visit = Visit::where('unique_id', '=', $id)->with(['patient', 'transactions'])->first();
        if ($visit !== null){
            return response()->json([
                'doctors' => Employee::select('id', 'user_id')->where('department_id', '=', 5)->with('user')->get(),
                'specialties' => Specialty::select('id', 'name')->orderBy('name')->with('doctors')->get(),
                //'symptoms' => Symptom::select('id', 'name', 'code', 'group_code')->orderBy('name', 'ASC')->get(), 
                'visit' => $visit,
            ]);
        }
        else{
            return response()->json([
                'status' => 'Error',
                'message' => 'Invalid Visit',
            ]);
        }
    }

}
