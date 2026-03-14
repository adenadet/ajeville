<?php 

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\Request as LaboratoryRequest;
use App\Models\EMR\Laboratory\RequestDetail;
use App\Models\EMR\Laboratory\Result as LaboratoryResult;
use App\Models\EMR\Laboratory\RequestItem;
use App\Models\EMR\Laboratory\ServiceAnalyte;
use App\Models\EMR\Visit;
use App\Models\EMR\VisitTransaction;
use App\Models\Inventory\Item;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaboratoryRequestService
{

    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function accept($request){
        return DB::transaction(function () use ($request) {

            $labRequest = LaboratoryRequest::with('lab_service.emr_service.item', 'lab_service.analytes')->lockForUpdate()->findOrFail($request->id);

            if ($labRequest->status !== LaboratoryRequest::StatusAccepted && $labRequest->status !== LaboratoryRequest::StatusBooked && $labRequest->status !== LaboratoryRequest::StatusAccepted ) {
                throw new Exception("Invalid request state transition.");
            }

            $transaction = VisitTransaction::lockForUpdate()->findOrFail($request->transaction_id);
            $transaction = $this->transactionService->transaction_payment($transaction)->fresh();
            
            if ($transaction->status === VisitTransaction::StatusPaid) {
                $service_analytes = $labRequest->lab_service->analytes;
                foreach ($service_analytes as $serviceAnalyte) {
                    LaboratoryResult::create([
                        'request_id' => $labRequest->id,
                        'analyte_id' => $serviceAnalyte->id,
                        'status' => LaboratoryResult::StatusPending,
                        'created_by' => auth('api')->id() ?? Auth::id(),
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]);
                }

                $labRequest->status = LaboratoryRequest::StatusStarted;
                $labRequest->save();
            }

            echo "Laboratory Request: ".$labRequest->status;

            return $labRequest->fresh(['result', 'lab_service.analytes', 'transaction.coverage']);
        });
    }

    public function create($visit_id, $patient_id, $consultation_id=null, $transaction, $item_id, $special, $date){
        return DB::transaction(function () use ($visit_id, $patient_id, $consultation_id, $transaction, $item_id, $special, $date) {
            $visit = Visit::findOrFail($visit_id);
            $item = Item::findOrFail($item_id);
            $request = LaboratoryRequest::create([
                'branch_id' => $visit->branch_id ?? request()->cookie('current_branch'),
                'consultation_id' => $consultation_id ?? null,
                'date' => $date ?? now(),
                'item_id' => $item_id,
                'laboratory_service_id' => $item->emr_service->reference->id,
                'ordered_by' => auth('api')->id() ?? Auth::id(),
                'patient_id' => $patient_id,
                'quantity' => 1,
                'transaction_id' => $transaction->id,
                'visit_id' => $visit_id,
                'status' => LaboratoryRequest::StatusBooked,
                'special' => $special,
                'created_by' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);

            return $request;
        });
    }

    public function start($request): void
    {
        DB::transaction(function () use ($request) {

            if ($request->status != LaboratoryRequest::StatusSampleCollected){
                throw new Exception('Request can not be started');
            }

            if (empty($request->result)){
                LaboratoryResult::create([
                    'request_id' => $request->id,
                    'service_id' => $request->laboratory_service_id,
                    'status' => LaboratoryResult::StatusPending,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id()
                ]);
            }

            $request->update([
                'status' => LaboratoryRequest::StatusOngoing,
                'collected_by' => auth('api')->id() ?? Auth::id(),
                'collected_at' => auth('api')->id() ?? Auth::id(),
                'updated_by' => auth('api')->id() ?? Auth::id(),
            ]);
        });
    }

    public function verify($request): void
    {
        DB::transaction(function () use ($request) {

            if ($request->request_items()
                ->whereNotIn('status', [RequestItem::StatusAccepted])
                ->exists()) {

                throw new Exception("All services must be completed before verification.");
            }

            $results = $request->request_items()->with('results')->get()->flatMap->results;

            if ($results()
                ->where('status', '!=', LaboratoryResult::StatusEntered)
                ->exists()) {

                throw new Exception("All results must be entered before verification.");
            }

            foreach ($results as $result) {
                $result->update([
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                    'status' => LaboratoryResult::StatusVerified
                ]);
            }

            $request->update([
                'status' => LaboratoryRequest::StatusConfirmed
            ]);
        });
    }

}

