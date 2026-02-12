<?php

namespace App\Http\Controllers\Api\EMR\Laboratory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Traits\EMR\LaboratoryTrait;

class BottleController extends Controller
{
    use LaboratoryTrait;

    public function destroy($id)
    {
        $bottle = $this->emr_laboratory_bottles_deactivate( $id);

        return response()->json([
            'bottle' => $bottle,
        ], is_string($bottle) ? 404 : 200);
    }

    public function index()
    {
        return response()->json([
            'bottles' => $this->emr_laboratory_bottles_get_all('active', $_GET, true, true),
        ]);
    }

    public function show($id)
    {
        $bottle = $this->emr_laboratory_bottles_get_by($id, true);
        return response()->json([
            'bottle' => $bottle,
        ], is_string($bottle) ? 404 : 201);
    }

    public function store(Request $request)
    {
        $bottle = $this->emr_laboratory_bottles_create($request);
        return response()->json([
            'bottle' => $bottle,
        ], is_string($bottle) ? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        $bottle = $this->emr_laboratory_bottles_update($request, $id);

        return response()->json([
            'bottle' => $bottle,
        ], is_string($bottle) ? 500 : 200);
    }
}
