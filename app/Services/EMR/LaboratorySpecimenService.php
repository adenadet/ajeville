<?php 

namespace App\Services\EMR;

use App\Http\Traits\EMR\LaboratoryTrait;
use App\Models\EMR\Laboratory\Request;
use App\Models\EMR\Laboratory\RequestItem;
use App\Models\EMR\Laboratory\LaboratoryResult;
use App\Models\EMR\Laboratory\Specimen;
use App\Models\EMR\Laboratory\SpecimenRejection;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaboratorySpecimenService
{
    use LaboratoryTrait;
    public function collect( $request, $data)
    {
        if ($request->status !== Request::StatusStarted && $request->status !== Request::StatusSampleCollected) {
            throw new Exception("Specimen cannot be collected at this stage.");
        }
        $barcode = $this->emr_laboratory_setting_generate_unique_id('specimen');
        $specimen = Specimen::create([
            'request_id' => $request->id,
            'unique_id' => $barcode,
            'barcode' => $barcode,
            'bottle_type_id' => $data['bottle_id'],
            'specimen_type_id' => $data['specimen_type_id'],
            'collected_by' => $data['collected_by'] ?? auth('api')->id() ?? Auth::id(),
            'collected_at' => $data['collected_at'] ?? now(),
            'status' => Specimen::StatusCollected,
            'remarks' => $data['remarks'],
            'created_by' => auth('api')->id() ?? Auth::id(), 
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);
        $request->status = Request::StatusSampleCollected;
        $request->save();
        return $specimen;
    }

    public function receive($specimen, $remark)
    {
        $specimen->update([
            'received_remark' => $remark,
            'received_by' => auth('api')->id() ?? Auth::id(),
            'received_at' => now(),
            'status' => Specimen::StatusReceived,
        ]);

        return $specimen;
    }

    public function reject($specimen, $data)
    {
        SpecimenRejection::create([
            'specimen_id' => $specimen->id,
            'reason' => $data['reason'],
            'remarks' => $data['remarks'],
            'created_by' => auth('api')->id() ?? Auth::id(),
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);

        $specimen->update([
            'status' => Specimen::StatusRejected,
            'updated_by' => auth('api')->id() ?? Auth::id(),
        ]);

        return $specimen;
    }
}
