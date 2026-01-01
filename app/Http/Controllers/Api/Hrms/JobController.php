<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\JobTrait;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use JobTrait;
    public function confirm(Request $request, $id)
    {
        return response()->json([
            'allowance' => $this->hrms_leave_allowance_confirm_request($request, $id), 
        ]);
    }

    public function destroy(string $id)
    {
        return response()->json([
            'allowance' => $this->hrms_leave_allowance_delete_request($id), 
        ]);
    }

    public function index()
    {
        return response()->json([
            'allowances' => $this->hrms_leave_allowance_get_all($_GET['type'], $_GET['status'], true, true, $_GET['page'] ?? 1), 
        ]);
    }
    
    public function show(string $id)
    {
        return response()->json([
            'allowance' => $this->hrms_leave_allowance_get_by_id($id), 
        ]);
    }
    
    public function store(Request $request)
    {
        return response()->json([
            'allowance' => $this->hrms_leave_allowance_create_request($request['employee_id'], $request['leave_request_id']), 
        ]);
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
