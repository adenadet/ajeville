<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\Operations\ServiceType;
use Illuminate\Http\Request;

use App\Http\Traits\Operations\ServiceTrait;

class ServiceTypeController extends Controller
{
    use ServiceTrait;
    
    public function destroy($id)
    {
        $service_type = $this->operation_service_type_deactivate($id);
        return response()->json([
            'service_type' => $service_type,
        ], is_string($service_type) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'service_types' => $this->operation_service_type_get_all('all', $_GET, true, true),
        ]);
    }

    public function store(Request $request)
    {
        $service_type = $this->operation_service_type_create($request);
        return response()->json([
            'service_type' => $service_type,
        ], is_string($service_type) ? 500 : 201);
    }

    public function show($id)
    {
        $service_type = $this->operation_service_type_get_by_id($id);
        return response()->json([
            'service_type' => $this->operation_service_type_get_by_id($id),
        ],is_string($service_type) ? 404 : 200);
    }

    public function update(Request $request, $id)
    {
        $service_type = $this->operation_service_type_update( $request, $id);
        return response()->json([
            'service_type' => $service_type,
        ], is_string($service_type) ? 500 : 200);
    }

}
