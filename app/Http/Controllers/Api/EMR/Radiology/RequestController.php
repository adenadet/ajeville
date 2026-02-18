<?php

namespace App\Http\Controllers\Api\EMR\Radiology;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\RadiologyTrait;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    use RadiologyTrait;
    public function destroy(string $id)
    {
        $radiology = $this->emr_radiology_request_deactivate($id);
        
        return response()->json([
            'request' => $radiology
        ], is_string($radiology) ? 500 : 200);
    }

    public function index()
    {
        $radiology = $this->emr_radiology_request_get_all($_GET['type'] ?? 'active', $_GET, true, true);
        
        return response()->json([
            'requests' => $radiology
        ]);
    }

    public function show(string $id)
    {
        $radiology = $this->emr_radiology_request_get_by('id', $id, true);
        
        return response()->json([
            'request' => $radiology
        ], is_string($radiology) ? 404 : 200);
    }

    public function store(Request $request)
    {
        /*$radiology = $this->emr_radiology_request_create($request);
        
        response()->json([
            'requests' => $radiology
        ]);*/
    }

    public function update(Request $request, string $id)
    {
        $radiology = $this->emr_radiology_request_update($request, $id);

        return response()->json([
            'requests' => $radiology
        ], is_string($radiology) ? 500 : 200);
    }
}
