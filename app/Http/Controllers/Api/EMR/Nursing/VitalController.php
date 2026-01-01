<?php

namespace App\Http\Controllers\Api\EMR\Nursing;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\QueueTrait;
use App\Http\Traits\EMR\NursingTrait;
use App\Models\EMR\Consultation\Consultation;
use App\Models\EMR\Nursing\Vital;

use Illuminate\Http\Request;


class VitalController extends Controller
{
    use NursingTrait;

    public function index()
    {
        return response()->json([
            'vitals' => $this->nursing_vitals_get_all($_GET['type'] ?? 'all', $_GET['status'] ?? null, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        $vital = $this->nursing_vitals_create($request);
        
        return response()->json([
            'vital' => $vital,
        ], is_string($vital) ? 422 : 201);
    }

    public function show($id)
    {
        return response()->json([
            'vital' => $this->nursing_vitals_get_by('id', $id, true),
            'message' => 'Task created successfully',
        ]);
    }
    
    public function update(Request $request, $id)
    {
        $vital = $this->nursing_vitals_update($request, $id);

        return response()->json([
            'vital' => $vital
        ], is_string($vital) ? 422 : 200);
    }

    public function destroy($id)
    {
        $vital = $this->nursing_vitals_delete( 'unique_id', $id);

        return response()->json([
            'vital' => $vital
        ], is_string($vital) ? 422 : 200);
    }
}