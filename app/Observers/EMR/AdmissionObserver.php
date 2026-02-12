<?php

namespace App\Observers\EMR;

use App\Models\EMR\Admission\BedAssignment;
use App\Models\EMR\Admission\Request as AdmissionRequest;
class AdmissionObserver
{
    public function updated(AdmissionRequest $admission)
    {
        if ($admission->isDirty('status') &&
            $admission->status === AdmissionRequest::StatusAdmitted) {

            BedAssignment::where('admission_id', $admission->id)
                ->where('status', BedAssignment::StatusPending)
                ->update([
                    'status' => BedAssignment::StatusAssigned,
                    'assigned_by' => auth('api')->id() ?? auth()->id(),
                    'assigned_at' => now(),
                    'updated_by' => auth('api')->id() ?? auth()->id(),
                ]);
        }
    }
}
