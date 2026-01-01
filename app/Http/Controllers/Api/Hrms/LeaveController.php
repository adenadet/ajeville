<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\EmployeeTrait;
use App\Http\Traits\Hrms\LeaveTrait;
use App\Mail\Leave\ConfirmMail;
use App\Mail\Leave\RejectMail;
use App\Mail\Leave\SupervisorConfirmMail;
use App\Models\Hrms\Employee;
use App\Models\Hrms\EmployeeLeaveType;
use App\Models\Hrms\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class LeaveController extends Controller
{
    use EmployeeTrait, LeaveTrait;
    public function assign_leave_types(Request $request){
        return response()->json([
            'assigned_leave_types' => $this->hrms_leave_employee_assign_leave_types($request->input('user_id'), $request->input('leave_types')),
        ]);
    }

    public function confirm(Request $request, $id){
        return response()->json([
            'leave_request' => $this->hrms_leave_request_confirm_leave($request, $id),
        ]);
    }

    public function destroy($id)
    {
        return response()->json([
            'request'     => $this->hrms_leave_request_delete_leave($id),
            'requests'    => $this->hrms_leave_request_get_all('my_leaves', null, true, true, $_GET['page'] ?? 1),    
        ]);
    }

    public function index()
    {
        return response()->json([
            'requests' => $this->hrms_leave_request_get_all($_GET['type'], $_GET['list_type'] ?? null, true, true, $_GET['page']),    
        ]);
    }

    public function initials()
    {
        return response()->json([
            'employees' => $this->hrms_employee_get_all('active', 'leave', null, false, null), 
            'my_leave_types' => $this->hrms_leave_types_get_my_current_leave_types(null, false, true),    
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'leave_type_id' => 'required|numeric',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'reason' => 'sometimes',
            'remarks' => 'sometimes',
            'is_half_day' => 'sometimes|boolean',
            'employee_id' => 'sometimes|numeric',
        ]);

        return response()->json([
            'leave_request' => $this->hrms_leave_request_create_leave($request),
        ]);

    }

    public function show($id)
    {
        return response()->json([
            'leave_request' => $this->hrms_leave_request_show_leave($id, $_GET['type']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'leave_type_id' => 'required|numeric',
            'start_date' => 'required|date',
            'to_date' => 'required|date',
            'description' => 'sometimes',
            'remarks' => 'sometimes',
            'is_half' => 'required|boolean',
        ]);

        return response()->json([
            'leave_request'     => $this->hrms_leave_request_update_leave($request, $id),
            'leave_requests'    => $this->hrms_leave_request_get_all('my_leaves', null, true, true, $_GET['page'] ?? 1),    
        ]);
    }
}
