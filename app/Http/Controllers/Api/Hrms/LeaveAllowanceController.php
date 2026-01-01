<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\LeaveAllowanceTrait;
use Illuminate\Http\Request;

class LeaveAllowanceController extends Controller
{
    use LeaveAllowanceTrait;
    public function confirm(Request $request, $id)
    {
        return response()->json([
            'allowance' => $this->hrms_leave_allowance_confirm_request($request, $id), 
        ]);
    }
    public function index()
    {
        return response()->json([
            'allowances' => $this->hrms_leave_allowance_get_all($_GET['type'], $_GET['status'], true, true, $_GET['page'] ?? 1), 
        ]);
    }

    public function store(Request $request)
    {
        return response()->json([
            'allowance' => $this->hrms_leave_allowance_create_request($request['employee_id'], $request['leave_request_id']), 
        ]);
    }

    public function show(string $id)
    {
        return response()->json([
            'allowance' => $this->hrms_leave_allowance_get_by_id($id), 
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        return response()->json([
            'allowance' => $this->hrms_leave_allowance_delete_request($id), 
        ]);
    }
}
