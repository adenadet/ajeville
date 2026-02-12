<?php

namespace App\Http\Controllers\Api\EMR\Admission;

use App\Http\Controllers\Controller;
use App\Http\Traits\EMR\AdmissionTrait;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
        use AdmissionTrait;
    
    public function destroy(string $id)
    {
        $room_type = $this->admission_room_type_deactivate($id);

        return response()->json([
            'room_type' => $room_type,
        ], is_string($room_type) ? 500 : 200);
    }

    public function index()
    {
        return response()->json([
            'room_types' => $this->admission_room_type_get_all($_GET['type'] ?? 'active', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'items' => $this->inventory_item_get_all('admission', null, false, false, null),
            'room_types' => $this->admission_room_type_get_all($_GET['type'] ?? 'active', $_GET, true, true),
        ]);
    }

    public function show(string $id)
    {
        $room_type = $this->admission_room_type_get_by(null, $id, true);

        return response()->json([
            'room_type' => $room_type
        ], is_string($room_type) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'sometimes|nullable',
            'status' => 'required|boolean',
        ]);
        
        $room_type = $this->admission_room_type_create($request);

        return response()->json([
            'room_type' => $room_type
        ], is_string($room_type) ? 500 : 201);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'sometimes|nullable',
            'status' => 'required|boolean',
        ]);    

        $room_type = $this->admission_room_type_update($request, $id);

        return response()->json([
            'room_type' => $room_type
        ], is_string($room_type) ? 500 : 200);
    }
}
