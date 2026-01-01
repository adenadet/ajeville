<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\EmployeeLeaveTypeTrait;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Hrms\LeaveTrait;
use App\Models\HRMS\Employee;
use App\Models\HRMS\EmployeeLeaveType;
use App\Models\HRMS\LeaveRequest;
use App\Models\HRMS\LeaveType;

use Illuminate\Http\Request;

class EmployeeLeaveTypeController extends Controller
{
    use EmployeeTrait, EmployeeLeaveTypeTrait, LeaveTrait;
    
    public function assigned(){
        return response()->json([
            'employee' => $this->hrms_employee_get_by_id(auth('api')->id(), 'staff'), 
            'user_leave_types' =>$this->hrms_leave_types_get_my_current_leave_types(null, true, true),
        ]);
    }
    public function index()
    {
        //
    }

    public function initials(){
        return response()->json([
            'leave_types' => $this->hrms_leave_type_get_all('allocate', null, false, false, null),
        ]);
    }

    public function store(Request $request)
    {
        //Add the checks here
        //Add to DB
        return response()->json([
            'employee_leave_type'   => $this->employee_leave_type_create($request),
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'assigned_leave_types' => $this->hrms_leave_type_get_assigned_by_id($id, true),
        ]);
    }

    public function update(Request $request, string $id)
    {
        //Add the checks here
        return response()->json([
            'employee_leave_type'   => $this->employee_leave_type_update($request, $id),
        ]);
    }

    public function destroy(string $id)
    {
        //Add checks here
        return response()->json([
            'employee_leave_type'   => $this->employee_leave_type_delete($id),
        ]);
    }
}
