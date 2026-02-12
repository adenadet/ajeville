<?php

namespace App\Http\Controllers\Api\EMR\Admission;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AdmissionTrait;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use AdmissionTrait;
    
    public function destroy(string $id)
    {
        $room = $this->admission_room_deactivate($id);

        return response()->json([
            'room' => $room
        ], is_string($room) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'rooms' => $this->admission_room_get_all($_GET['type'] ?? 'active', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'room_types' => $this->admission_room_type_get_all('active', $_GET, false, false),
            'wards' => $this->admission_ward_get_all('branch', $_GET, false, false),
        ]);
    }

    public function show(string $id)
    {
        $room = $this->admission_room_get_by(null, $id, true);

        return response()->json([
            'beds' => $this->admission_bed_get_all('active', ['room_id' => $id], true, true),
            'room' => $room
        ], is_string($room) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ward_id' => 'required|exists:emr_admission_wards,id',
            'room_type_id'   => 'required|exists:emr_admission_room_types,id',
            'name' => 'required|string|max:30',
        ]);
        
        $room = $this->admission_room_create($request);

        return response()->json([
            'room' => $room
        ], is_string($room) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'room_type_id' => 'required|exists:emr_admission_room_types,id',
            'ward_id' => 'required|numeric|exists:emr_admission_wards,id',
            'status' => 'required|boolean',
        ]);    

        $room = $this->admission_room_update($request, $id);

        return response()->json([
            'room' => $room
        ], is_string($room) ? 500 : 200);
    }
}