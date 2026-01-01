<?php

namespace App\Http\Controllers\Api\Ums;

use App\Http\Controllers\Controller;
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

use App\Models\EMR\Patient\Patient;

use Spatie\Permission\Models\Role;

use App\Http\Traits\Ums\UserTrait;
class UserController extends Controller
{
    use UserTrait;

    public function auth()
    {
        $user = User::find(auth('api')->id());
        return response()->json([
            'roles' => $user->getRoleNames(),
            'user' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        
        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'branches' => Branch::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'nok' => NextOfKin::where('user_id', auth('api')->id())->get(),
            'states' => State::orderBy('name', 'ASC')->get(),       
            'users' => $this->user_get_all(),
            'message' => 'The user '.$user->first_name.' '.$user->last_name.' has been deleted',
            'status' => 'success', 
            'user' => $user,
        ]);
    }

    public function details(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'nationality_id' => 'required',
            'passport_no' => 'required',
        ]);

        $patient = Patient::find($request->input('user_id'));

        $patient->nationality_id = $request->input('nationality_id');
        $patient->passport_no = $request->input('passport_no');

        $patient->save();
    }

    public function index()
    {
        $areas = Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get();
        $branches = Branch::select('id', 'name')->orderBy('name', 'ASC')->get();
        $departments = Department::select('id', 'name')->orderBy('name', 'ASC')->get();
        $nok = NextOfKin::where('user_id', auth('api')->id())->get();
        $states = State::orderBy('name', 'ASC')->get();
        $roles = Role::all();
        $users = $this->ums_user_get_all($_GET['type'] ?? 'all', $_GET['query'] ?? null, true, true);
        
        return response()->json([
            'areas' => $areas,
            'branches' => $branches,
            'departments' => $departments,
            'nok' => $nok,
            'roles' => $roles,
            'states' => $states,       
            'users' => $users,
        ]);
    }

    public function initials()
    {
        return response()->json(['users' => $this->ums_user_get_all('all', null, false, false,)]);
    }

    public function resend_link($id)
    {
        $mail = $this->ums_user_resend_link($id);
        return response()->json(['mail' => $mail], is_string($mail) ? 400 : 200);
    }
      
    public function roles(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|numeric',
            'roles' => 'required|array',
        ]);

        $user = User::find($request->input('user_id'));
        //$roles = Role::select('id', 'name')->whereIn('id', $request->input('roles'))->get();
        $user->syncRoles($request->input('roles'));

        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'branches' => Branch::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'nok' => NextOfKin::where('user_id', auth('api')->id())->get(),
            'states' => State::orderBy('name', 'ASC')->with(['areas'])->get(),       
            'users' => $this->ums_user_get_all(),
        ]);
    }

    public function search()
    {
        if ($search = $_GET['q']){
            $users = User::orderBy('first_name', 'ASC')->with('area')->with('state')->with('branch')->with('department')->where(function($query) use ($search){
                $query->where('first_name', 'LIKE', "%$search%")
                ->orWhere('middle_name', 'LIKE', "%$search%")
                ->orWhere('last_name', 'LIKE', "%$search%")
                ->orWhere('email', 'LIKE', "%$search%");
                })->paginate(52);
            }
        else{
            $users = User::orderBy('first_name', 'ASC')->with('area')->with('state')->with('branch')->with('department')->paginate(52);
        }
        
        return response()->json(['users' => $users,]);
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
        ]);

        $user = $this->ums_user_create($request);

        return response()->json([
            'users' => $this->ums_user_get_all(),
            'user' => $user,
        ]);
    }

    public function password(Request $request)
    {
        $this->validate($request, [
            'npw' => 'required|min:8|required_with:cpw|same:cpw',
            'opw' => 'required',
            'cpw' => 'required|min:8',
        ]);

        $user = User::find(auth('api')->id());
        
        $user->password = bcrypt($request->npw);
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'Your password has been changed successfully']);
        
    }
    
    public function profile()
    {
        $areas = Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get();
        $branches = Branch::select('id', 'name')->orderBy('name', 'ASC')->get();
        $departments = Department::select('id', 'name')->orderBy('name', 'ASC')->get();
        $nok = NextOfKin::where('user_id', auth('api')->id())->first();
        $states = State::orderBy('name', 'ASC')->get();
        $user = User::where('id', auth('api')->id())->with('area')->with('state')->with('branch')->first();
        
        return response()->json([
            'nations' => Country::orderBy('name', 'ASC')->get(),
            'areas' => $areas,
            'user' => $user,
            'branches' => $branches,
            'departments' => $departments,
            'nok' => $nok,
            'states' => $states,
            //'patient' => Patient::where('user_id',  auth('api')->id())->first(),       
        ]);
    }
    
    public function show($id)
    {
        $user = User::find($id);
        $areas = Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get();
        $branches = Branch::select('id', 'name')->orderBy('name', 'ASC')->get();
        $departments = Department::select('id', 'name')->orderBy('name', 'ASC')->get();
        $nok = NextOfKin::where('user_id', auth('api')->id())->get();
        $states = State::orderBy('name', 'ASC')->get();
        $users = $this->user_get_all();

        return response()->json([
            'nations' => Country::orderBy('name', 'ASC')->get(),
            'areas' => $areas,
            'branches' => $branches,
            'departments' => $departments,
            'nok' => $nok,
            'roles' => $user->getRoleNames(),
            'states' => $states,
            'user' => $user,
            //'patient' => Patient::where('user_id',  auth('api')->id())->first(),       
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
            'sex' => 'required|string',
            'dob' => 'required|date',
        ]);

        $user = $this->ums_user_update($request, $id);

        return response()->json([
            'message' => 'Your password has been changed successfully',
            'status' => 'success', 
            'user' => $user,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth('api')->user();

        return response()->json(['status' => 'Successful']);
    }
}
