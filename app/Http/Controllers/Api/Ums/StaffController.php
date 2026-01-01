<?php

namespace App\Http\Controllers\Api\Ums;

use App\Http\Controllers\Controller;
use App\Http\Traits\Hrms\EmployeeTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Department;
use App\Models\NextOfKin;
use App\Models\Staff;
use App\Models\State;
use App\Models\User;

use App\Models\EMR\Patient;

use Spatie\Permission\Models\Role;

use App\Http\Traits\Ums\UserTrait;

class StaffController extends Controller
{
    use EmployeeTrait, UserTrait;
    public function destroy($id)
    {
        $staff = $this->user_staff_deactivate_by_id($id);
        $areas = Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get();
        $branches = Branch::select('id', 'name')->orderBy('name', 'ASC')->get();
        $departments = Department::select('id', 'name')->orderBy('name', 'ASC')->get();
        $nok = NextOfKin::where('user_id', auth('api')->id())->get();
        $states = State::orderBy('name', 'ASC')->get();
        
        return response()->json([
            'staff' => $staff,
            'areas' => $areas,
            'branches' => $branches,
            'departments' => $departments,
            'nok' => $nok,
            'states' => $states,       
            'users' => $this->user_get_all(),
        ]);
    }
    public function index()
    {
        $areas = Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get();
        $branches = Branch::select('id', 'name')->orderBy('name', 'ASC')->get();
        $departments = Department::select('id', 'name')->orderBy('name', 'ASC')->get();
        $nok = NextOfKin::where('user_id', auth('api')->id())->get();
        $states = State::orderBy('name', 'ASC')->get();
        
        return response()->json([
            'areas' => $areas,
            'branches' => $branches,
            'departments' => $departments,
            'employees' => $this->user_staffs_get_all('all', null, true, true, $_GET['page'] ?? 1),
            'nok' => $nok,
            'states' => $states,       
            'users' => $this->user_get_all(),
        ]);
    }
    
    public function latest(){
        return response()->json([
            'staffs' => User::where('user_type', '!=', 'Applicant')->orderBy('created_at', 'DESC')->limit(8)->get(),
        ]);
    }
    public function show($id)
    {
        return response()->json([
            'employee' => $this->hrms_employee_get_by_id($id, $_GET['viewer'] ?? null),
            'staff' => $this->hrms_employee_get_by_id($id, $_GET['viewer'] ?? null),       
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'sometimes',
            'street2' => 'sometimes',
            'city' => 'required',
            'state_id' => 'numeric',
            'area_id' => 'numeric',
            'phone' => 'numeric',
            'alt_phone' => 'nullable|numeric',
            'sex' => 'required|string',
            'dob' => 'required|date',
            'supervisor_id' => 'sometimes|numeric',
        ]);

        $user = $this->user_create_new($request, null);

        return response()->json([
            // This are the required for User page
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'branches' => Branch::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'nok' => NextOfKin::where('user_id', auth('api')->id())->get(),
            'states' => State::orderBy('name', 'ASC')->get(),       
            'users' => $this->user_get_all(),
            'message' => 'Your password has been changed successfully',
            'status' => 'success', 
            'user' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'street' => 'sometimes',
            'street2' => 'sometimes',
            'city' => 'required',
            'state_id' => 'numeric',
            'area_id' => 'numeric',
            'phone' => 'numeric',
            'alt_phone' => 'nullable|numeric',
            'branch_id' => 'required|numeric',
            'sex' => 'required|string',
            'dob' => 'required|date',
        ]);

        $user = $this->user_update_user($request, $id);

        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'branches' => Branch::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'nok' => NextOfKin::where('user_id', auth('api')->id())->get(),
            'states' => State::orderBy('name', 'ASC')->get(),       
            'users' => $this->user_get_all(),
            'message' => 'Your password has been changed successfully',
            'status' => 'success', 
            'user' => $user,
        ]);
    }
}
