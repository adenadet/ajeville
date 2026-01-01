<?php

namespace App\Http\Controllers\Api\Operations;

use App\Http\Controllers\Controller;
use App\Http\Traits\Operations\ServiceTypeTrait;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    use ServiceTypeTrait;
    public function destroy(string $id)
    {
        $service_type = $this->operation_service_type_delete($id);
        return response()->json([
            'service_types'    => $service_type
        ], is_string($service_type) ? 400 : 200);
    }

    public function index()
    {
        return response()->json([
            'service_types'    => $this->operation_service_type_get_all($_GET['status'] ?? 'active',  $_GET, true, true),
        ]);
    }

    public function show(string $id)
    {
        $service_type = $this->operation_service_type_get_by_id($id);
        return response()->json([
            'service_type'    => $service_type,
        ], is_string($service_type) ? 400 : 200);
    }

    public function store(Request $request)
    {
        $service_type = $this->operation_service_type_create($request->all());
        return response()->json([
            'service_type'    => $service_type,
        ], is_string($service_type) ? 400 : 200);
    }

    public function update(Request $request, string $id)
    {
        $service_type = $this->operation_service_type_update( $request->all(), $id);
        return response()->json([
            'service_type'    => $service_type,
        ], is_string($service_type) ? 400 : 200);
    }
}
