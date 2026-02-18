<?php 

namespace App\Services\EMR;

use App\Models\EMR\Laboratory\Request;
use App\Models\EMR\Laboratory\RequestDetail;
use App\Models\EMR\Laboratory\LaboratoryResult;
use App\Models\EMR\Laboratory\ResultFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class LaboratoryVendorResultIntegrationService
{
    public function uploadFile($outsourceOrder, $file)
    {
        return DB::transaction(function () use ($outsourceOrder, $file) {

            $path = $file->store('uploads/laboratory/vendor-results');

            ResultFile::create([
                'outsource_order_id' => $outsourceOrder->id,
                'file_path' => $path,
                'uploaded_by' => auth('api')->id() ?? Auth::id(),
                'uploaded_at' => now(),
                'created_by' => auth('api')->id() ?? Auth::id(),
            ]);

            $outsourceOrder->update([
                'status' => 50
            ]);
        });
    }
}
