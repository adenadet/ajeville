<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\LaboratoryTrait;
use App\Models\EMR\Laboratory\Result;
use App\Services\EMR\LaboratoryResultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultsController extends Controller
{
    use LaboratoryTrait;
    protected $service;

    public function __construct(LaboratoryResultService $service)
    {
        $this->service = $service;
    }

    public function delete($id)
    {
        $result = Result::findOrFail($id);

        $result->delete();

        return response()->json([
            'message' => 'Result deleted'
        ]);
    }


    public function initials($id)
    {
        $result = Result::with(['request.patient.user', 'request.lab_service.analytes', 'request.lab_service.service_analytes.analyte.reference_ranges', 'latestVersion.values.analyte',])->where('request_id', '=', $id)->latest()->firstOrFail();
        return response()->json([
            'analytes' => $this->service->initial($result->request),
            'result' => $result,
            'specimens' => $this->emr_laboratory_specimen_get_all('received', ['patient_id' => $result->request->patient_id], true, false)
        ]);
    }

    public function index(Request $request)
    {
        $query = Result::query()->with(['request.patient', 'latestVersion']);

        if ($request->patient_id) {
            $query->whereHas('request', function ($q) use ($request) {
                $q->where('patient_id', $request->patient_id);
            });
        }

        if ($request->status) {$query->where('status', $request->status);}

        return response()->json(
            $query->paginate(20)
        );
    }

    public function show($id)
    {
        return Result::with([
            'request.patient',
            'versions.values.analyte',
            'reviews',
            'files'
        ])->findOrFail($id);
    }
    
    public function store(Request $request)
    {
        $result = $this->service->create($request);

        return response()->json([
            'result' => $result
        ]);
    }

    public function update(Request $request, $id)
    {
        $result = Result::findOrFail($id);
        $version = $result->latestVersion;
        $this->service->saveValues($version, $request->values);
        return response()->json([
            'message' => 'Result saved'
        ]);
    }

    public function verify(Request $request)
    {

        $result = Result::findOrFail($request->result_id);
        //echo "Result ID: ".$result." ,\n Decision: ".$request->decision.", \n Remarks: ".$request->remarks.", \n Reason:".$request->reason ?? '';

        $this->service->verify(
            $result,
            $request->decision,
            $request->remarks,
            $request->reason ?? '',
        );

        return response()->json([
            'message' => 'Verification completed'
        ]);
    }
}
