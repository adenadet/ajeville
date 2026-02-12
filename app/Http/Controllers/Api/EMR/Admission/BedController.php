<?php

namespace App\Http\Controllers\Api\EMR\Admission;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AdmissionTrait;
use Illuminate\Http\Request;

class BedController extends Controller
{
    use AdmissionTrait;
    
    public function destroy(string $id)
    {
        $bed = $this->admission_bed_deactivate($id);

        return response()->json([
            'bed' => $bed
        ], is_string($bed) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'beds' => $this->admission_bed_get_all($_GET['type'] ?? 'active', $_GET, true, true),
        ]);
    }

    public function release(string $id)
    {
        $bed = $this->admission_bed_assignment_release(null, $id);

        return response()->json([
            'bed' => $bed
        ], is_string($bed) ? 500 : 200);
    }


    public function show(string $id)
    {
        $bed = $this->admission_bed_get_by(null, $id, true);

        return response()->json([
            'bed' => $bed
        ], is_string($bed) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'room_id'=> 'required|exists:emr_admission_rooms,id',
        ]);
        
        $bed = $this->admission_bed_create($request);

        return response()->json([
            'bed' => $bed
        ], is_string($bed) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'bed_id'     => 'required|exists:beds,id',
            'all_visit'  => 'nullable|boolean',
        ]);    

        $bed = $this->admission_bed_update($request, $id);

        return response()->json([
            'bed' => $bed
        ], is_string($bed) ? 500 : 200);
    }
}
