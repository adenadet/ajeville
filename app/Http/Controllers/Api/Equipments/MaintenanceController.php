<?php

namespace App\Http\Controllers\Api\Equipments;

use App\Http\Controllers\Controller;
use App\Http\Traits\Equipments\MaintenanceTrait;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    use MaintenanceTrait;
    public function destroy(string $id)
    {
        $ticket = $this->equipment_maintenance_ticket_deactivate($id);

        return response()->json(['ticket' => $ticket], is_string($ticket) ? 500 :200);
    }

    public function index()
    {
        $tickets = $this->equipment_maintenance_ticket_get_all($_GET['type'] ?? 'all', $_GET['specific'] ?? null,true, true, $_GET['page'] ?? null);

        return response()->json(['tickets' => $tickets], is_string($tickets) ? 500 :200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,uuid',
            'issue_description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:operation_departments,id',
            'status' => 'nullable|integer',
            'current_description' => 'nullable|string',
            'started_date' => 'nullable|date',
        ]);

        $ticket = $this->equipment_maintenance_ticket_create($validated);

        return response()->json(['ticket' => $ticket], is_string($ticket) ? 500 : 201);
    }

    public function show(string $id)
    {
        $ticket = $this->equipment_maintenance_ticket_get_by('uuid', $id, true);

        return response()->json(['ticket' => $ticket], is_string($ticket) ? 500 : 200);
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,uuid',
            'issue_description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:operation_departments,id',
            'status' => 'nullable|integer',
            'current_description' => 'nullable|string',
            'started_date' => 'nullable|date',
        ]);

        $ticket = $this->equipment_maintenance_ticket_update($validated, $id);

        return response()->json(['ticket' => $ticket], is_string($ticket) ? 500 : 201);
    }

}
