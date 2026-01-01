<?php

namespace App\Http\Controllers\Api\Ums;

use App\Http\Controllers\Controller;
use App\Http\Traits\Ums\UserTrait;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Department;
use App\Models\NextOfKin;
use App\Models\Staff;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RegistrationController extends Controller
{

    use UserTrait;

    public function index()
    {
        return response()->json([
            'birthdays'     => User::birthDayBetween(Carbon::now(), Carbon::now()->addWeek())->limit(8)->get(),
        ]);
    }

    public function initials(){
        return response()->json([
            'areas' => Area::select('id', 'name')->where('state_id', 25)->orderBy('name', 'ASC')->get(),
            'states' => State::with('areas')->orderBy('name', 'ASC')->get(),
        ]);
    }
    
    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        return response()->json([
            'user' => $this->ums_user_get_by('registration_token', $id, true),
        ]);
    }

    public function update(Request $request, string $id)
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
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = $this->ums_user_update($request, $id);
        
        //$password = 
        $this->ums_user_change_password($user->id, $request->password);
        
        if(is_string($user)){
            
        }
        return response()->json([
            'message' => 'Your password has been changed successfully',
            'status' => 'success', 
            'user' => $user,
        ]);
    }

    public function destroy(string $id)
    {
        //
    }
}