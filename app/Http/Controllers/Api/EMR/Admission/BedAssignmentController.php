<?php

namespace App\Http\Controllers\Api\EMR\Admission;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AdmissionTrait;
use Illuminate\Http\Request;

class BedAssignmentController extends Controller
{
    use AdmissionTrait;
    
    public function destroy(string $id)
    {
        $bed_assignment = $_GET['action'] == 'cancel' ? $this->admission_bed_assignment_deactivate($id) : $this->admission_bed_assignment_release($_GET, $id);

        return response()->json([
            'bed_assignment' => $bed_assignment
        ], is_string($bed_assignment) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'bed_assignments' => $this->admission_bed_assignment_get_all($_GET['type'] ?? 'active', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'beds' => $this->admission_bed_get_all('active', null, true, false),
            'rooms' => $this->admission_room_get_all('active', null, true, false),
            'wards' => $this->admission_ward_get_all('active', null, true, false),
        ]);
    }

    public function show(string $id)
    {
        $bed_assignment = $this->admission_bed_assignment_get_by(null, $id, true);

        return response()->json([
            'bed_assignment' => $bed_assignment
        ], is_string($bed_assignment) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bed_id'       => 'required|exists:emr_admission_beds,id',
            'patient_id'   => 'required|exists:emr_patients,id',
            'admission_id' => 'required|exists:emr_admission_requests,id',
        ]);
        
        $bed_assignment = $this->admission_bed_assignment_create($request);

        return response()->json([
            'bed_assignment' => $bed_assignment
        ], is_string($bed_assignment) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'bed_id'     => 'required|exists:beds,id',
            'all_visit'  => 'nullable|boolean',
        ]);    

        $bed_assignment = $this->admission_bed_assignment_update($request, $id);

        return response()->json([
            'bed_assignment' => $bed_assignment
        ], is_string($bed_assignment) ? 500 : 200);
    }
}