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
}