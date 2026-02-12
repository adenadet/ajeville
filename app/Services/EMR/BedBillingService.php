<?php

namespace App\Services\EMR;

use App\Http\Traits\EMR\VisitTransactionTrait;
use App\Models\EMR\Admission\Request as AdmissionRequest;
use App\Models\EMR\Admission\BedAssignment;
use App\Models\EMR\Patient\Patient;
use App\Models\EMR\VisitTransaction;

class BedBillingService
{
    use VisitTransactionTrait;

    public function createCharge(BedAssignment $assignment, $num = 1)
    {
        $room_type = $assignment->bed->room_type;

        return $this->emr_visit_transaction_create(
            $room_type->item_id,
            $assignment->patient_id,
            $num,
            true,
            $assignment->admission->visit_id
        );
    }

    public function createChargeForRoomType($roomType, $patientId, $visitId, $num = 1)
    {
        return $this->emr_visit_transaction_create($roomType->item_id, $patientId, $num, true, $visitId);
    }

    public function deleteBedCharge($assignment)
    {
        $query = VisitTransaction::where('visit_id', $assignment->admission->visit_id)
            ->where('item_id', $assignment->bed->room_type->item_id)
            ->where('patient_id', $assignment->patient_id);

        return $query->update([
            'deleted_at' => now(),
            'deleted_by' => auth('api')->id(),
        ]);
    }

    public function reverseBedCharge($assignment, $allVisit = false)
    {
        $query = VisitTransaction::where('visit_id', $assignment->admission->visit_id)
            ->where('item_id', $assignment->bed->room_type->item_id)
            ->where('patient_id', $assignment->patient_id)
            ->where('status', '=', VisitTransaction::StatusCompleted);

        if (!$allVisit) {$query->latest()->limit(1);}

        $customer = Patient::findOrFail($assignment->patient_id);
        $customer->balance += $query->sum('amount');
        $customer->save();

        return $query->update([
            'deleted_at' => now(),
            'deleted_by' => auth('api')->id(),
        ]);
    }
}
