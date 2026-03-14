<?php

namespace App\Services\EMR;

use App\Http\Traits\EMR\AdmissionTrait;
use App\Http\Traits\EMR\ConsultationTrait;
use App\Http\Traits\EMR\DialysisTrait;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Http\Traits\EMR\RadiologyTrait;
use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Models\EMR\VisitTransaction;
use App\Models\EMR\VisitPaymentAllocation;
use App\Models\EMR\VisitPayment;
use App\Models\EMR\VisitTransactionCoverage;
use App\Models\Inventory\Item;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    use AdmissionTrait, ConsultationTrait, DialysisTrait, LaboratoryTrait, RadiologyTrait, VisitTransactionTrait;

    protected $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function create_transaction(array $data)
    {
        return DB::transaction(function () use ($data) {
            $item = Item::with('service_type')->findOrFail($data['item_id']);
            //echo $item->service_type->name;
            return match ($item->service_type->name) {
                'Admission' => $this->admission_request_create($data),
                'Consultation' => $this->emr_consultation_create_request($data),
                'Dialysis' => $this->emr_dialysis_request_create($data),
                'Laboratory' => $this->emr_laboratory_request_create($data['patient_id'], $item->id,$data['visit_id'], $data['consultation_id'], $data['date'] ?? now(), $data['special'] ?? 0),
                'Radiology' => $this->emr_radiology_request_create($data['patient_id'],$item->id, $data['visit_id'], $data['consultation_id'], $data['date'] ?? now(), $data['special'] ?? 0),
                default => throw new Exception('Unsupported service type')
            };
        });
    }

    public function resolve_transaction_payment_status(VisitTransaction $transaction){
        $transaction->load('ledger_entries');
        $ledgerPaid = $transaction->ledger_entries()->where('direction', '=', 'DR')->sum('amount');

        echo "Total Ledger payment: ".$ledgerPaid."<br />";
        $total = $transaction->item_total;
        $coverage = $transaction->coverage;

        if ($coverage) {
            echo "Seen as covered \n";
            if ($coverage->approval_status !== 'approved') {
                $transaction->status = VisitTransaction::StatusAwaitingApproval;
                $transaction->save();
                return $transaction;
            }

            $coveredAmount = $coverage->covered_amount;
            $paidTotal = $coveredAmount + $ledgerPaid;

            if ($paidTotal >= $total) {$transaction->status = VisitTransaction::StatusPaid;} 
            else {$transaction->status = VisitTransaction::StatusAwaitingCoPayment;}
        }
        else {
            echo "Seen as not covered \n";
            if ($ledgerPaid >= $total) {$transaction->status = VisitTransaction::StatusPaid;} 
            else {$transaction->status = VisitTransaction::StatusPending;}
        }

        echo "Total Transaction Status: ".$transaction->status."<br />";
        $transaction->save();
        return $transaction;
    }

    public function transaction_payment($transaction){
        if ($transaction->status == VisitTransaction::StatusPending){
            $this->ledgerService->createLedgerEntry($transaction->patient_id, $transaction->visit_id, $transaction->id, null, 'debit', 'debit');
        }

        return $this->resolve_transaction_payment_status($transaction);
    }
}