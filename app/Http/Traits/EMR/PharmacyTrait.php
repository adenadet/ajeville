<?php
namespace App\Http\Traits\EMR;
use App\Http\Traits\Finance\TransactionTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Models\EMR\Drugs\Drug;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\Pharmacy\Prescription;
use App\Models\EMR\Pharmacy\PrescriptionDrug;
use App\Models\EMR\Pharmacy\PrescriptionFulfill;
use App\Models\Inventory\Item;
use App\Models\Inventory\StoreItemBatch;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait PharmacyTrait
{
    use FileManagerTrait, LogTrait, TransactionTrait;

    public function emr_pharmacy_drug_create($data){
        DB::beginTransaction();

        try{
            $drug = Drug::create([
                'name' => $data['name'],
                'ham' => $data['ham'],
                'status' => $data['status'],
                'description' => $data['description'],
                'interactions' => $data['interactions'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);    
            $this->$this->log_user_activity('Drug created', $drug->id, true);
            DB::commit();
            return $drug;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Drug created', null, false);
            return $e->getMessage();
        }
    }

    public function emr_pharmacy_drug_get_all($type, $specific, $detailed, $paginated, $page){
        switch($type){
            case 'active':
                $query = Drug::where('status', '=', 1);
            break;
            case 'all':
                $query = Drug::withTrashed();
            break;
            case 'inactive':
                $query = Drug::where('status', '!=', 1)->withTrashed();
            break;
        }

        $query = $query->orderBy('name', 'ASC');

        $query = $detailed ? $query->with(['specific_drugs']) : $query->select('id', 'name');
        $query = $paginated ? $query->paginate(50) : $query->get();

        return $query;
    }

    public function emr_pharmacy_drug_update($data, $id){
        DB::beginTransaction();

        try{
            $drug = Drug::find($id);
            
            $drug->name = $data['name'];
            $drug->ham = $data['ham'];
            $drug->status = $data['status'];
            $drug->description = $data['description'];
            $drug->interactions = $data['interactions'];
            $drug->updated_by = Auth::id() ?? auth('api')->id();

            $drug->save();
               
            $this->$this->log_user_activity('Drug updated', $id, true);
            DB::commit();
            return $drug;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Drug updated', $id, false);
            return $e->getMessage();
        }
    }

    public function emr_pharmacy_prescription_create($data)
    {
        DB::beginTransaction();
        try{
            $prescription = Prescription::create([
                'visit_id' => $data['visit_id'],
                'consultation_id' => $data['consultation_id'] ?? null,
                'patient_id' => $data['patient_id'],
                'doctor_id' => Auth::id() ?? auth('api')->id(),
                'doctor_name' => "Dr. " . Auth::user()->first_name . " " . Auth::user()->last_name,
                'date' => $data['date'] ?? date('Y-m-d'),
                'refill_count' => $data['refill_count'] ?? 0,
                'valid_till' => isset($data['refill_count']) || $data['refill_count'] > 0 ? ($data['valid_till'] ?? date('Y-m-d', strtotime('+1 year'))) : null,
                'status' => $data['status'] ?? 1,
                'start_date' => $data['start_date'] ?? date('Y-m-d'),
                'end_date' => $data['end_date'] ?? date('Y-m-d', strtotime('+1 year')),
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            if (isset($data['prescription_drugs']) && is_array($data['prescription_drugs'])) {
                foreach ($data['prescription_drugs'] as $drug) {
                    PrescriptionDrug::create([
                        'prescription_id' => $prescription->id,
                        'drug_id' => $drug['drug_id'],
                        'specific_drug_id' => $drug['specific_drug_id'] ?? null,
                        'drug_name' => $drug['drug_name'],
                        'detail' => $drug['detail'] ?? null,
                        'dose' => $drug['dose'],
                        'duration' => $drug['duration'],
                        'frequency' => $drug['frequency'],
                        'form' => $drug['form'],
                        'route' => $drug['route'],
                        'quantity' => $drug['quantity'],
                        'start_date' => $drug['start_date'] ?? date('Y-m-d'),
                        'end_date' => $drug['end_date'] ?? null,
                    ]);
                }
            }

            $this->$this->log_user_activity('Prescription created', $prescription->id, true);
            DB::commit();
            return $prescription;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Prescription created', null, false);
            return $e->getMessage();
        }
    }

    public function emr_pharmacy_prescription_fulfillable_batch($drug_id, $user_store_id){
        $items = Item::where('specific_id', '=', $drug_id)->where('category_id', '=', 2)->pluck('id');
        $batches = StoreItemBatch::where('store_id', '=', $user_store_id)->whereIn('item_id', $items)->with(['batch_id', 'item'])->get();

        return $batches;
    }

    public function emr_pharmacy_prescription_fulfillment($data, $id)
    {
        $prescription = PrescriptionDrug::where('id', '=', $id)->first();
        
        $prescription_drug_fulfill = "";
        /*if ($drug) {
            // Mark the prescription drug as fulfilled
            $prescription_drug->fulfilled = true;
            $prescription_drug->save();

            // Create a transaction for the patient
            $transaction_data = [
                'user_id' => $user_store->user_id,
                'amount' => $prescription_drug->price,
                'description' => 'Prescription fulfillment for drug ID: ' . $prescription_drug->drug_id,
            ];

            $this->finance_transaction_create($item_id, $patient_id, $quantity, $auto_debit = false, $visit_id = NULL);

            return true;
        }

        return false;
        */
    }

    public function emr_pharmacy_prescription_get_all($type, $specific, $detailed, $paginated, $page)
    {
        switch ($type) {
            case 'all':
                $query = is_null($specific) || !isset($specific) ? Prescription::withTrashed() : Prescription::withTrashed()->whereDate('date', '>=', $specific['start_date'])->whereDate('date', '<=', $specific['end_date']);
            break;
            case 'consultation':
                $query = Prescription::withTrashed()->where('consultation_id', '=', $specific['consultation_id']);
            break;
            case 'doctor':
                $query = Prescription::withTrashed()->where('doctor_id', '=', $specific['doctor_id']);
                if(isset($specific['start_date']) && $specific['start_date'] != ''){
                    $query = $query->whereDate('date', '>=', $specific['start_date']);
                }
                if(isset($specific['end_date']) && $specific['end_date'] != ''){
                    $query = $query->whereDate('date', '<=', $specific['end_date']);
                }
            break;
            case 'patient':
                $query = Prescription::where('patient_id', '=', $specific['patient_id']);
                if(isset($specific['start_date']) && $specific['start_date'] != ''){
                    $query = $query->whereDate('date', '>=', $specific['start_date']);
                }
                if(isset($specific['end_date']) && $specific['end_date'] != ''){
                    $query = $query->whereDate('date', '<=', $specific['end_date']);
                }
            break;
            case 'refillables':
                $refillables = Prescription::where('refill_count', '>', 0)->pluck('id');
                $query = Prescription::whereIn('id', $refillables);
                if(isset($specific['patient_name']) && $specific['patient_name'] != ''){
                    $users = User::where('first_name', 'like', '%'.$specific['patient_name'].'%')->orWhere('middle_name', 'like', '%'.$specific['patient_name'].'%')->orWhere('last_name', 'like', '%'.$specific['patient_name'].'%')->pluck('id');
                    $patients = Patient::whereIn('user_id', $users)->pluck('id');
                    $query = $query->whereIn('patient_id', $patients)->orderBy('date', 'DESC');
                }
                if(isset($specific['start_date']) && $specific['start_date'] != ''){
                    $query = $query->whereDate('date', '>=', $specific['start_date']);
                }
                if(isset($specific['end_date']) && $specific['end_date'] != ''){
                    $query = $query->whereDate('date', '<=', $specific['end_date']);
                }
            break;
            case 'status':
                $query = Prescription::withTrashed()->where('status', '=', $specific['status']);
            break;
            case 'visit':
                $query = Prescription::where('visit_id', '=', $specific['visit_id']);
            break;
            default:
                $query = Prescription::withTrashed();
            break;
        }

        $query = $detailed ? $query->with(['consultation', 'doctor', 'prescription_drugs', 'patient', 'visit']) : $query->select('id', 'visit_id', 'consultation_id', 'patient_id', 'date', 'doctor_id', 'refill_count', 'valid_till', 'status');
        $query = $paginated ? $query->paginate(30) : $query->get();
        
        return $query;
    }
}