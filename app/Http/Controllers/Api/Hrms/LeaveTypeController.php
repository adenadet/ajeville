<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\LeaveTrait;
use App\Models\Department;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    use LeaveTrait;
    
    public function assign(Request $request){
        return response()->json([
            'leave_types' =>$this->hrms_leave_employee_assign_leave_types($request->input('employee_id'), $request->input('leave_types')),
        ]);
    }

    public function assigned($id){
        return response()->json([
            'user_leave_types' =>$this->hrms_leave_types_get_my_current_leave_types(null, true, true),
        ]);
    }

    public function index()
    {
        return response()->json([
            'leave_types' => $this->hrms_leave_type_get_all('all', null, true, true, $_GET['page'] ?? 1),    
        ]);
    }

    public function initials()
    {
        return response()->json([
            'departments' => Department::with('employees.user')->get(),
            'employees' => $this->hrms_employee_get_all('active', null, false, false, null),
            'leave_types' => $this->hrms_leave_type_get_all('active', null, false, false, null),    
        ]);
    }
    
    public function show($id)
    {
        return response()->json([
            'assigned' => $this->hrms_leave_type_get_assigned_by_id($id),
            'leave_type' => $this->hrms_leave_type_get_by_id($id, true),
            'requests' => $this->hrms_leave_request_get_all('leave_type', $id, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:8',
            'leave_category' => 'required',
            'no_of_days' => 'required|numeric',
            'status' => 'required|boolean',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable',
        ]);

        $leave_type = $this->hrms_leave_types_create_type($request);
        return response()->json([
            'leave_type' => $leave_type,   
        ], is_string($leave_type) ? 500 : 201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:8',
            'no_of_days' => 'required|numeric',
            'status' => 'required|boolean',
            'start_date' => 'sometimes|date|nullable',
            'end_date' => 'sometimes|date|nullable',
        ]);

        $leave_type = $this->hrms_leave_types_update_type($request, $id);
        return response()->json([
            'leave_type'=> $leave_type,
        ], is_string($leave_type) ? 500 : 200);
    }

    public function destroy($id)
    {
        return response()->json([
            'leave_type'=> $this->hrms_leave_types_delete_by_id($id),
            'leave_types' => $this->hrms_leave_type_get_all_types('all', true, true),    
        ]);
    }
}
