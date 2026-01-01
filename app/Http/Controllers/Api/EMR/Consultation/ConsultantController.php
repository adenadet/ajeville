<?php

namespace App\Http\Controllers\Api\EMR\Consultation;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\ConsultationTrait;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\PharmacyTrait;
use App\Http\Traits\EMR\RadiologyTrait;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\Inventory\ItemTrait;
use App\Models\Operations\Branch;
use Illuminate\Http\Request;

use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Consultation\Specialty;
use App\Models\EMR\Drugs\Drug;
use App\Models\EMR\Drugs\Form as DrugForm;
use App\Models\EMR\Drugs\Route as DrugRoute;
//use App\Models\EMR\Frequency;
use App\Models\EMR\Laboratory\Request as LaboratoryRequest;
use App\Models\EMR\Pharmacy\Prescription;
use App\Models\EMR\Prescription\Drug as PrescriptionDrug;
use App\Models\EMR\Radiology\Request as RadiologyRequest;
use App\Models\EMR\Settings\Duration;
use App\Models\EMR\Settings\Frequency;
use App\Models\EMR\Settings\ICD10;
use App\Models\EMR\Settings\Location;
use App\Models\EMR\Settings\Position;
use App\Models\EMR\Settings\Symptom;
use App\Models\EMR\Visit;
use App\Models\Finance\PriceListItem;
use App\Models\Finance\Transaction;
use App\Models\Inventory\Item;

class ConsultantController extends Controller
{
    use ConsultationTrait, ItemTrait, LaboratoryTrait, PharmacyTrait, RadiologyTrait, TransactionTrait;

    public function doctor_queue()
    {
        return response()->json([
            'consultations' => $this->consultant_queue_doctor(),
        ]);
    }

    public function index()
    {
        return response()->json([
            'consultations' =>  $this->emr_consultation_get_all($_GET['type'] ?? 'all', $_GET['specific'] ?? null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'drugs' => Drug::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'durations' => Duration::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'drug_forms' => DrugForm::select('name')->orderBy('name', 'ASC')->get(),
            'frequencies' => Frequency::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'icd_10_codes' => ICD10::select('id', 'icd10_code', 'icd10_3_code_description')->orderBy('code', 'ASC')->get(),
            'laboratory_services' => $this->inventory_item_get_all('classification_name', 'Laboratory', false, false, null),
            'locations' => Location::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'positions' => Position::select('id','name')->orderBy('name', 'ASC')->get(),
            'radiology_services' => $this->inventory_item_get_all('classification_name', 'Radiology', false, false, null),
            'routes' => DrugRoute::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'specific_drugs' => $this->inventory_item_get_all('classification_name', 'Drugs', false, false, null),
            'symptoms' => Symptom::all(),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'consultation' => Consultation::where('id', '=', $id)->with(['consultation_type', 'patient.user', 'specialty', 'transaction', 'laboratory.item', 'radiology.test', 'prescriptions.drugs'])->first(),
            /*'durations' => Duration::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'drug_forms' => DrugForm::select('name')->orderBy('name', 'ASC')->get(),
            //'frequencies' => Frequency::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'icd_10_codes' => ICD10::select('id', 'code', 'name')->orderBy('code', 'ASC')->get(),
            'laboratory_services' => $this->inventory_item_get_all('classification_name', 'Laboratory', false, false, null),
            'locations' => Location::select('id', 'name')->orderBy('name', 'ASC')->get(),
            //'patient' => 
            'positions' => Position::select('id','name')->orderBy('name', 'ASC')->get(),
            'radiology_services' => $this->inventory_item_get_all('classification_name', 'Radiology', false, false, null),
            'routes' => DrugRoute::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'specific_drugs' => $this->inventory_item_get_all('classification_name', 'Drugs', false, false, null),
            *///'symptoms' => Symptom::select('id', 'name', 'code', 'group_code')->orderBy('name', 'ASC')->get(),
        ]);
    }


    public function start($id, $force = 0)
    {
        // Bill Patient
        $consultation = Consultation::where('id', '=', $id)->with(['visit', 'patient.user',])->first();

        if (!is_null($consultation->transaction_id)) {
            $transaction = Transaction::where('id', '=', $consultation->transaction_id)->first();
            if ($transaction->status == 0) {
                $this->payTransaction($consultation->transaction_id);
            }
        } else {
            return  response()->json(['message' => "No bill to be paid",  'status' == 'Paid']);
        }
    }

    public function store(Request $request)
    {
        $this->save_consultation($request);

        return response()->json([
            'consultations' => Consultation::where('status', '=', 1)->with(['consultation_type', 'patient.user', 'specialty', 'transaction'])->latest()->paginate(20),
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function my_past_consultations()
    {
        return  response()->json([
            'consultations' => Consultation::where('consultant_seen_id', '=', auth('api')->id())->with(['consultation_type', 'patient.user', 'specialty', 'transaction'])->latest()->paginate(20),
            'message' => "No bill to be paid",  'status' == 'Paid'
        ]);
    }

    public function destroy($id)
    {
        //
    }
}
