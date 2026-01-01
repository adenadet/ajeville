<?php

namespace App\Http\Controllers\Api\EMR\Hims;

use App\Http\Controllers\Controller;
use App\Http\Traits\Operations\ServiceType;
use Illuminate\Http\Request;

use App\Http\Traits\Operations\ServiceTypeTrait;

class ServiceTypeController extends Controller
{
    use ServiceTypeTrait;
    
    public function destroy($id)
    {
        $service_type = $this->operation_service_type_deactivate($id);
        return response()->json([
            'service_types' => $this->operation_service_type_get_all(true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function index()
    {
        return response()->json([
            'service_types' => $this->operation_service_type_get_all(true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'service_type' => $this->operation_service_type_create($request),
            'service_types' => $this->operation_service_type_get_all(true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'service_type' => $this->operation_service_type_get_by_id($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'service_type' => $this->operation_service_type_update( $request, $id),
            'service_types' => $this->operation_service_type_get_all(true, true, $_GET['page'] ?? 1),
        ]);
    }

}
