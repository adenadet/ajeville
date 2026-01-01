<?php

namespace App\Http\Controllers\Api\Operations;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\EmployeeTrait;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Department;
use App\Models\HRMS\Employee;

use App\Http\Traits\Operations\DepartmentTrait;
use App\Http\Traits\Ums\UserTrait;

class DepartmentController extends Controller
{
    use DepartmentTrait, EmployeeTrait, UserTrait;
    public function index()
    {
        return response()->json([
            'departments' => $this->operation_department_get_all_departments(true, true, $_GET['page'] ?? 1)           
        ]);        
    }

    public function initials()
    {
        return response()->json([
            'employees' => $this->hrms_employee_get_all('active', null, false, false, null),             
        ]);        
    }

    public function store(Request $request)
    {
        $department = $this->operation_department_create($request);

        return response()->json([
            'departments' => $this->operation_department_get_all_departments(true, true, $_GET['page'] ?? 1),
            'department' => $department,         
        ], is_string($department) ? 500 : 201);
    }

    public function show($id)
    {
        return response()->json([
            'department' => $this->operation_department_get_department_by_id($id, true)            
        ]);
    }

    public function update(Request $request, $id)
    {
        $department = $this->operation_department_update($request, $id);

        return response()->json([
            'departments' => $this->operation_department_get_all_departments(true, true, $_GET['page'] ?? 1)            
        ]);
    }

    public function destroy($id)
    {
        $department = $this->operation_department_delete($id);

        return response()->json([
            'departments' => $this->operation_department_get_all_departments(true, true, $_GET['page'] ?? 1)              
        ]); 
    }
}
