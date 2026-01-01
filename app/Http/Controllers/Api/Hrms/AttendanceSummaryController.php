<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceSummaryController extends Controller
{
    public function destroy(string $id)
    {
        $attendance_summary = $this->hrms_attendance_summary_delete($id);
    }

    public function index()
    {
        //
    }

    public function initials()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'shift_id'   => 'required|exists:shifts,id',
            'employee_ids' => 'nullable|array',
            'department_id' => 'nullable|exists:departments,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $attendance_summary = $this->hrms_attendance_summary_create($request);

        return response()->json(['attendance_summary' => $attendance_summary], is_string($attendance_summary) ? 400 : 201);
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
}
