<?php

namespace App\Http\Controllers\Api\Hrms;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\DesignationTrait;
use App\Http\Traits\Operations\DepartmentTrait;
use App\Models\Hrms\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    use DepartmentTrait, DesignationTrait;
    public function destroy(string $id)
    {
        $designation = $this->hrms_designation_deactivate($id);
        return response()->json(['designation' => $designation,], is_string($designation) ? 500 : 200);
    }
 
    public function index()
    {
        return response()->json([
            'designations' => $this->hrms_designation_get_all('active', null, true, true)
            //Designation::with(['department', 'employees.user', 'employees.department', 'unit'])->orderBy('name', 'ASC')->paginate(40),
        ]);
    }

    public function initials()
    {
        return response()->json([
            'departments' => $this->operation_department_get_all_departments(false, false, null),
        ]);
    }

    public function search(string $id)
    {
        return response()->json([
            'designations' => $this->hrms_designation_get_all('search', $id, true, true)
        ]);
    }
    public function show(string $id)
    {
        return response()->json([
            'designation' => $this->hrms_designation_get_by(null, $id, true) 
            //Designation::where('id', '=', $id)->with(['department', 'employees.user', 'employees.department', 'unit'])->first()
        ]);
    }

    public function store(Request $request)
    {
        $designation = $this->hrms_designation_create($request);

        return response()->json([
            'designation' => $designation,
            'designations' => $this->hrms_designation_get_all('active', null, true, true),
        ], is_string($designation) ? 500 : 200);
    }

    public function update(Request $request, string $id)
    {
        $designation = $this->hrms_designation_update($request, $id);
        
        return response()->json([
            'designation' => $designation,
            'designations' => $this->hrms_designation_get_all('active', null, true, true),
        ], is_string($designation) ? 500 : 200);
    }

}
