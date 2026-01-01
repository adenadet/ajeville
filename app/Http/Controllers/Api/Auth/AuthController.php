<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\Ums\UserTrait;
use App\Models\Ums\UserOTP;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use UserTrait;
    public function complete_registration_via_otp(Request $request)
    {
        $this->validate($request, [
            'otp' => 'required|numeric|min:6',
        ]);

        $otp = $this->ums_user_verify_otp( auth('api')->id() ?? Auth::id(), 'Email Verification',$request->input('otp'));

        return response()->json([
            'message' => $otp ? __('app.otp_verified') : __('app.otp_invalid'),
        ], is_string($otp) ? 422 : 200);

    }

    public function complete_registration(Request $request)
    {
        $this->validate($request, [
            'unique_id' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', '=', $request->input('unique_id'))->OrWhere('unique_id', '=', $request->input('unique_id'))->first();

        if (!$user || !(Hash::check($request->password, $user->password))){
            return response()->json([
                'message' => __('auth.failed'),
            ], 422);
        }

        $token = $user->createToken('nairafy_app')->accessToken;

        return response()->json([
            'message' => $user->email_verified_at ? __('app.login_success') : __('app.login_success_verify'),
            'user' => new UserResource($user),
            'token' => $token,
        ]);

    }

    public function get_email_verification_otp()
    {
        $otp = $this->ums_user_generate_otp(auth('api')->id() ?? Auth::id(), 'Email Verification', 'api');

        return response()->json([
            'message' => $otp ? __('app.otp_send_success') : __('app.otp_send_failed'),
        ]);

    }
    public function login(Request $request)
    {
        $this->validate($request, [
            'unique_id' => 'required|string',
            'password' => 'required|min:6',
        ]);

        $user = User::where('email', '=', $request->input('unique_id'))->OrWhere('unique_id', '=', $request->input('unique_id'))->first();

        if (!$user || !(Hash::check($request->password, $user->password))){
            return response()->json([
                'message' => __('auth.failed'),
            ], 422);
        }

        $token = $user->createToken('nairafy_app')->accessToken;

        return response()->json([
            'message' => $user->email_verified_at ? __('app.login_success') : __('app.login_success_verify'),
            'user' => new UserResource($user),
            'token' => $token,
        ]);

    }


    public function register(Request $request)
    {
        //Validate the request
        $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'unique_id' => 'sometimes|nullable|string',
            'email' => 'required|email|unique:users,email',
            'phone' => 'sometimes|nullable|numeric|min:10',
            'password' => 'required|min:8|max:255',
        ]);
        //Create user
        $user = $this->ums_user_create($request);
        
        //Create token
        $token = $user->createToken('nairafy_app')->accessToken;

        //Return Values
        return response()->json([
            'message' => __('app.registration_success'),
            'user' => new UserResource($user),
            'token' => $token,
        ]);

    }

    public function reset(Request $request)
    {
        //Validate the request
        $this->validate($request, [
            'email' => 'required|email|exists:users,email|max:255',
            'password' => 'required|min:8|max:255',
            'verification_code' => 'required|string',
        ]);
        $otp = UserOTP::where('code', $request->input('verification_code'))->where('type', '=', 'password_reset')->where('status', 1)->first();
        if (!$otp){
            return response()->json([
                'message' => __('app.otp_invalid'),
            ], 422);
        }
    
        $user = User::where('email', '=', $request->input('email'))->first();
        if ((!$user) || ($user->id != $otp->user_id)){
            return response()->json([
                'message' => __('app.user_not_found'),
            ], 422);
        }
        else{
            $user->password = Hash::make($request->input('password'));
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();
        }
        
        //Create token
        $token = $user->createToken('nairafy_app')->accessToken;

        //Return Values
        return response()->json([
            'message' => __('app.password_reset_successful'),
            'user' => new UserResource($user),
            'token' => $token,
        ]);

    }

    public function reset_otp(Request $request)
    {
        //Validate the request
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
        ]);
        //Create user
        $user = User::where('email', '=', $request->input('email'))->first();
        if (!$user){
            return response()->json([
                'message' => __('auth.failed'),
            ], 405);
        }
        else{
            $otp = $this->ums_user_generate_otp($user->id, 'password_reset', 'api');
        
            return response()->json([
                'message' => is_string($otp) ? __('app.otp_send_failed') : __('app.otp_send_success'),
            ], is_string($otp) ? 405 : 200);
        }
    }
}
