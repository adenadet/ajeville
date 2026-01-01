<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\AttendanceTrait;
use App\Http\Traits\Hrms\BasicTrait;
use App\Http\Traits\Hrms\EmployeeLeaveTypeTrait;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Hrms\LeaveAllowanceTrait;
use App\Http\Traits\Hrms\LeaveTrait;
use App\Http\Traits\Hrms\SalaryTrait;
use App\Models\Hrms\Employee;
use App\Models\Hrms\LeaveAllowance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use AttendanceTrait, BasicTrait, EmployeeTrait, EmployeeLeaveTypeTrait, LeaveTrait, LeaveAllowanceTrait, SalaryTrait; 
    public function admin()
    {
        $employee = Employee::where('user_id', Auth::id() ?? auth('api')->id())->first();
        return response()->json([
            'active_leaves' => $this->hrms_leave_request_get_all('active', null, false, false, null)->count(),
            'new_staffs' => $this->hrms_attendance_summary_get_all('mine', ['start_date' => date('Y-m-01', strtotime(date('Y-m-01').' -1 MONTH')), 'end_date' => date('Y-m-t', strtotime(date('Y-m-01').' -1 MONTH'))], true, false, null ),
            'pending_applications' => $this->hrms_employee_get_single('user_id', Auth::id() ?? auth('api')->id(), true),
            'pending_leave_allowances' => $this->hrms_employee_get_single('user_id', Auth::id() ?? auth('api')->id(), true),
            'pending_resignations' => $this->hrms_employee_get_single('user_id', Auth::id() ?? auth('api')->id(), true),
            'staff_requests' => $this->hrms_leave_employee_assigned_leave_types($employee->id),
            'staff_strength' => $this->hrms_leave_employee_assigned_leave_types($employee->id),
        ]);
    }
    public function index()
    {
        $employee = Employee::where('user_id', Auth::id() ?? auth('api')->id())->first();
        return response()->json([
            'attendance_summaries' => $this->hrms_attendance_summary_get_all('mine', ['start_date' => date('Y-m-01', strtotime(date('Y-m-01').' -1 MONTH')), 'end_date' => date('Y-m-t', strtotime(date('Y-m-01').' -1 MONTH'))], true, false, null ),
            'employee' => $this->hrms_employee_get_single('user_id', Auth::id() ?? auth('api')->id(), true),
            'employee_leave_types' => $this->hrms_leave_employee_assigned_leave_types($employee->id),
        ]);
    }

    public function store(Request $request)
    {
        

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if($id == 'admin'){
            return response()->json([
                'employee' => $this->hrms_employee_get_single('user_id', Auth::id() ?? auth('api')->id(), true),
                'employee_leave_types' => $this->employee_leave_type_get_all('user_id', Auth::id() ?? auth('api')->id(), true, true, null),
                'leave_types' => $this->hrms_leave_allowance_get_all('user_id', Auth::id()?? auth('api')->id(), true, true, null),
                'leave_allowances' => $this->hrms_leave_allowance_get_all('status', LeaveAllowance::StatusPending, true, true, null),
            ]); 
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
