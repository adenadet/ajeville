<?php 

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\Request;
use App\Models\EMR\Laboratory\RequestItem;
use App\Models\EMR\Laboratory\LaboratoryResult;
use App\Models\EMR\Laboratory\Specimen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaboratorySpecimenService
{
    public function collect( $request_item, $data)
    {
        if ($request_item->status !== RequestItem::StatusConfirmed) {
            throw new \Exception("Specimen cannot be collected at this stage.");
        }

        return Specimen::create([
            'request_id' => $request_item->id, 
            'barcode' => null, 
            'collected_by' => $data['collected_by'] ?? auth('api')->id() ?? Auth::id(),
            'collected_at' => $data['collected_at'] ?? now(),
            'status' => Specimen::StatusCollected,
            'created_by' => auth('api')->id() ?? Auth::id(), 
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);
    }

    public function receive($specimen)
    {
        $specimen->update([
            'received_by' => auth('api')->id() ?? Auth::id(),
            'received_at' => now(),
            'status' => Specimen::StatusReceived,
        ]);
    }

    public function reject($specimen, string $reason)
    {
        $specimen->update([
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
            'status' => Specimen::StatusRejected,
        ]);
    }
}
