<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\EMR\ConsultationTrait;
#use App\Http\Traits\EMR\DialysisTrait;
#use App\Http\Traits\EMR\EmergencyTrait;
#use App\Http\Traits\EMR\QueueTrait;
use App\Http\Traits\EMR\InsuranceTrait;
use App\Http\Traits\EMR\PatientTrait;
use App\Http\Traits\EMR\VisitTrait;
use App\Http\Traits\Finance\TransactionTrait;

use App\Models\EMR\Service;
use App\Models\EMR\Settings\ServiceType;
use App\Models\EMR\Visit;
use App\Models\EMR\Patient\Patient;
#use App\Models\EMR\VisitType;

use App\Models\Finance\Transaction;
use App\Models\Finance\PriceList;
use App\Models\Insurance\ProviderType;
use App\Models\Inventory\Item;
use App\Models\Operations\Branch;
use Exception;
use Illuminate\Support\Facades\DB;


class VisitationController extends Controller
{
    use ConsultationTrait, InsuranceTrait, PatientTrait, VisitTrait; //DialysisTrait, EmergencyTrait, QueueTrait, TransactionTrait
    public function bills($id){
        $branch_id = request()->cookie('current_branch');
        $visit = $this->visit_get_by_unique_id($id);
        $transactions = Transaction::where('visit_id', '=', $visit->id)->where('status', '=', 1)->with([ 'service_type'])->get();
        return response()->json([
            'visit' => $visit,
            'transactions' => $transactions,
        ]);
    }

    public function destroy($id)
    {
        $visit = Visit::where('id', '=', $id)->first();

        $visit->end_date = date('Y-m-d');
        $visit->end_timestamp = date('Y-m-d H:i:s');
        $visit->status = 2;
        $visit->updated_by = auth('api')->id();

        $visit->save();

        return response()->json([
            'visit' => $visit,
            'visits' => Visit::whereDate('start_date', '<=', $visit->end_date)->whereNull('end_date')->with(['patient.user', 'visit_type'])->latest()->paginate(50),
        ]);
    }

    public function end(Request $request, $id)
    {
        $visit = Visit::find($id);

        $visit->status = 2;
        $visit->end_date = date('Y-m-d');
        $visit->end_timestamp = date('Y-m-d H:i:s');
        $visit->updated_by = auth('api')->id();
        $visit->updated_at = date('Y-m-d H:i:s');

        $visit->save();

        return response()->json([
            'visit' => $visit,
            'visits' => $this->emr_visit_get_all('all', request()->cookie('current_branch'), null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function get_cookie()
    {
        $visit = $this->emr_visit_get_by(null, request()->cookie('current_visit'), true);
        return response()->json([
            'visit' => $visit,
        ], is_string($visit) ? 500 : 200);
    }

    public function index()
    {
        $date = date('Y-m-d');
        //$branch_id = request()->cookie('current_branch');
        return response()->json([
            'visits' => $this->emr_visit_get_all('active', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'branches' => Branch::where('status','=', 1)->orderBy('name', 'ASC')->get(),
            'patients' => Patient::with(['user', 'insurances.plan.provider'])->orderBy('unique_id', 'ASC')->get(),
            'plans' => $this->insurance_provider_plan_get_all('branch', null, false, false, null),
            'providers' => $this->insurance_provider_get_all('active', null, true, false, null),
            'insurance_types' => ProviderType::select('id', 'name')->get(),
            'service_types' => ServiceType::select('id', 'name')->orderBy('name', 'ASC')->get(),
        ]);
    }

    public function outpatient()
    {
        $date = date('Y-m-d');
        $visitations = Visit::where('date', '=', $date)->paginate(50);
        return response()->json([
            'visits' => $visitations,
        ]);
    }

    public function set_cookie(Request $request)
    {
        $visit = $request->input('visit');
        $cookie = cookie('current_visit', $visit['id'], 3600);
        return response('Cookie has been set'.$visit['id'])->cookie($cookie);
    }

    public function start(Request $request, $id)
    {
        $visit = $this->visit_start($id);
        return response()->json([
            'visit' => $visit,
            'visits' => $this->visit_get_all('all', request()->cookie('current_branch'), null, true, true, $_GET['page'] ?? 1),
            'visit_types' => $this->visit_type_get_all('queuable', null, false, false, null),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'patient_id' => 'required|numeric',
            'visit_type_id' => 'required|numeric',
            'branch_id' => 'required|numeric',
            'start_date' => 'required|date',
            'plan_id' => 'required|numeric',
        ]);

        $past_visit = $this->emr_visit_patient_pending($request['patient_id']);

        if (!is_null($past_visit)){
            return response()->json([
                'status' => "Error",
                'message' => "Previous Visit not closed",
                'past_visits' => $past_visit,
                'count_visit' => count($past_visit),
            ]);
        }

        $visit = $this->emr_visit_create($request);
        
        return response()->json([
            'status' => "Completed",
            'message' => "Visit created successfully",
            'visit' => $visit,
            'patient' => $this->emr_patient_get_by_id(null, $visit->patient_id, true),
        ], is_string($visit) ? 500 : 201);
    }

    public function show($id)
    {
        if (!empty($id)){
            $visit = $this->emr_visit_get_by(null, $id, true);
            $patient = is_string($visit) ? null : $visit->patient;
                
            return response()->json([
                'visit' => $visit,
                'patient' => $patient,
            ], is_string($visit) ? 500 : 200);
        }
        else{
            return response()->json(['visit' => null, 'patient' => null,]);
        }
    }

    public function transactions(Request $request)
    {
        $services = $request->input('items');

        /*foreach($services as $service){
            $this->createTransaction($service['id'], $request->input('patient_id'), $service['quantity'], false, $request->input('visit_id'));            
        }*/

        $visit = Visit::where('id', '=', $request->input('visit_id'))->with(['patient.user', 'visit_type', 'price_list.price_list_items', 'transactions.service_type',])->first();
        $patient = Patient::where('id', '=', $visit->patient_id)->with(['user', 'insurances.plan.provider'])->first();
        return response()->json([
            'visit' => $visit,
            'patient' => $patient,
        ]);
    }

    public function update(Request $request, $id)
    {
        
    }

}
