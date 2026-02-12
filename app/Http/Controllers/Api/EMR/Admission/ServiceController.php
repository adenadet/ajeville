<?php

namespace App\Http\Controllers\Api\EMR\Admission;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AdmissionTrait;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use AdmissionTrait;
    
    public function destroy(string $id)
    {
        $service = $this->admission_service_deactivate($id);

        return response()->json([
            'service' => $service
        ], is_string($service) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'services' => $this->admission_service_get_all($_GET['type'] ?? 'active', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'categories' => $this->admission_category_get_all('active', null, false, false),
        ]);
    }

    public function show(string $id)
    {
        $service = $this->admission_service_get_by(null, $id, true);

        return response()->json([
            'service' => $service
        ], is_string($service) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|exists:emr_admission_categories,id',
            'name' => 'required|string|max:30',
        ]);
        
        $service = $this->admission_service_create($request);

        return response()->json([
            'service' => $service
        ], is_string($service) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:emr_admission_categories,id',
            'status' => 'nullable|boolean',
        ]);    

        $service = $this->admission_service_update($request, $id);

        return response()->json([
            'service' => $service
        ], is_string($service) ? 500 : 200);
    }
}
