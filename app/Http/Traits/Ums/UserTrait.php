<?php
namespace App\Http\Traits\Ums;
use App\Http\Traits\General\FileTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Mail\Ums\CompleteRegistrationMail;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Department;
use App\Models\NextOfKin;
use App\Models\Staff;
use App\Models\State;
use App\Models\User;

use App\Models\EMR\Patient;
use App\Models\HRMS\Employee;
use App\Models\Hrms\UserAccount;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


trait UserTrait{
    use FileManagerTrait, LogTrait;

    public function ums_user_add_role($user_id, $role_name){
        $user = User::find($user_id);
        $role = Role::where('name', '=', $role_name)->first();
        if ($user->hasRole($role->name)){
            return 'User already has this role';
        }
        else{
            $user->assignRole($role->name);
            return 'Role assigned successfully';
        }
    }

    public function ums_user_change_password($user_id, $password){
        DB::beginTransaction();

        try{
            $user = User::find($user_id);
            $user->password = bcrypt($password);
            $user->updated_at = date('Y-m-d H:i:s');
            $user->save();
            DB::commit();   
            return $user;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }
    public function ums_user_create($data){
        $image_url = (!empty($data['image'])) ? $this->file_upload_to_location($data['image'], 'image', 'img/profile/', null) : 'default.png';

        $user = User::create([
            'email' => $data['email'] ?? null,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'street' => $data['street'] ?? null,
            'street2' => $data['street2'] ?? null,
            'city' => $data['city'] ?? null,
            'state_id' => $data['state_id'] ?? null,
            'area_id' => $data['area_id'] ?? null,
            'phone' => $data['phone']?? null,
            'alt_phone' => $data['alt_phone'] ?? null,
            'sex' => $data['sex']?? null,
            'dob' => $data['dob']?? null,
            'image' => $image_url,
            'unique_id' => $data['username'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'joined_at' => $data['joined_at'] ?? null,
            'password' => bcrypt('asdfasdf'),
        ]);
        return $user;
    }

    public function ums_user_create_temporary_user($data){
        $user = User::where('email', '=', $data['email'])->first();
        if($user){return $user;}
        else{
            $user = User::create([
                'email'         => $data['email'],
                'image'         => 'default.png',
                'name'          => $data['name'] ?? $data['first_name'].' '.$data['last_name'],
                'phone'         => $data['phone'],
                'registration_token' => Str::random(40),
            ]);
        }
        
        try {
            Mail::to($user->email)->send(new CompleteRegistrationMail($user));
        } 
        catch (Exception $e) {
            //Log::error('Mail sending failed: ' . $e->getMessage());
        }
        //Mail::to($user->email)->send(new CompleteRegistrationMail($user));
        return $user;
    }

    public function ums_user_get_all($type = null, $specific = null, $detailed = null, $paginated = null){
        $query = User::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', '=', 'active');
            break;
            case 'inactive':
                $query = $query->where('status', '=', 'inactive');
            break;
        }
        $query = $detailed ? $query->with(['area', 'branch', 'department', 'roles', 'state']) : $query->select('id', 'first_name', 'last_name', 'middle_name', 'unique_id');
        $query = $query->orderBy('first_name', 'ASC');
        $query = $paginated ? $query->paginate(52) : $query->get();
        return $query;
    }

    public function ums_user_get_by($type, $specific, $detailed){
        $query = User::where('id', '=', $specific)->orWhere('unique_id', '=', $specific)->orWhere('registration_token', '=', $specific);
        
        $query = $detailed ? $query->with(['area', 'branch', 'department', 'roles', 'state']) : $query->select('id', 'first_name', 'last_name', 'middle_name', 'unique_id');
        
        return $query->first();
    }

    public function ums_user_remove_role($user_id, $role_name){
        $user = User::find($user_id);
        $role = Role::where('name', '=', $role_name)->first();
        if ($user->hasRole($role->name)){
            $user->removeRole($role->name);
            return 'Role removed successfully';
        }
        else{
            return 'User does not have this role';
        }
    }

    public function ums_user_resend_link($user_id){
        try{
            $user = User::findOrFail($user_id);
            $mail = Mail::to($user->email)->send(new CompleteRegistrationMail($user));
            if (count(Mail::failures()) > 0) {
            return 'Failures: ' . implode(',', Mail::failures());
            }
            return ['message' => 'Mail sent (no failures reported)', 'details' => $mail];

        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function ums_user_update($data, $id){
        DB::beginTransaction();
        try{
            $image_url = (!is_null($data['image']) && $data['image'] != 'default.png') ? $this->file_upload_to_location($data['image'], 'image', 'img/profile/', null) : 'default.png';
            
            $user = User::find($id);

            $user->email = $data['email'] ?? $user->email;
            $user->first_name = $data['first_name'] ?? $user->first_name;
            $user->middle_name = $data['middle_name'] ?? $user->middle_name;
            $user->last_name = $data['last_name'] ?? $user->last_name;
            $user->name = ($data['last_name'] ?? $user->last_name).', '.($data['first_name'] ?? $user->first_name);
            $user->street = $data['street'] ?? $user->street;
            $user->street2 = $data['street2'] ?? $user->street2;
            $user->city = $data['city'] ?? $user->city;
            $user->state_id = $data['state_id'] ?? $user->state_id;
            $user->area_id = $data['area_id'] ?? $user->area_id;
            $user->phone = $data['phone'] ?? $user->phone;
            $user->alt_phone = $data['alt_phone'] ?? $user->alt_phone;
            $user->sex = $data['sex'] ?? $user->sex;
            $user->dob = $data['dob'] ?? $user->dob;
            $user->image = $image_url;
            $user->updated_at = date('Y-m-d H:i:s');
            
            $user->save();
            DB::commit();   
            return $user;
        }
        catch(Exception $e){
            DB::rollBack();
            return $e->getMessage();
        }
    }

    /*
    ___________________________________________________________________

    User Account 
    ___________________________________________________________________
    */

    public function ums_user_account_create($data){
        DB::beginTransaction();

        try{

            $query = UserAccount::create([
                'user_id' => $data['user_id'],
                'bank_id' => $data['bank_id'],
                'account_name' => $data['account_name'],
                'account_number' => $data['account_number'],
                'primary_account' => $data['primary_account'],
                'status' => $data['status'] ?? 1,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();   
            $this->log_user_activity('User Account Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Account Create', null, false);
            return $e->getMessage();
        }
    }

    public function ums_user_account_deactivate($id){
        DB::beginTransaction();
        try{
            $query = UserAccount::findOrFail($id);

            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            if($query->status == 1){
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s');
                $query->status = 0;
            }
            else{
                $query->deleted_by = null;
                $query->deleted_at = null;
                $query->status = 1;
            }
            $query->save();
            
            DB::commit();   
            $this->log_user_activity('User Account Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Account Deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function ums_user_account_make_primary($id){
        DB::beginTransaction();
        try{
            $query = UserAccount::findOrFail($id);

            UserAccount::where('user_id', '=', $query->user_id)->update(['primary_account' => 0, 'updated_by' => auth('api')->id() ?? Auth::id()]);
            
            $query->primary_account = 1;
            $query->deleted_at = null;
            $query->deleted_by = null;
            $query->updated_by = auth('api')->id() ?? Auth::id();

            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function ums_user_account_get_all($type, $specific, $detailed, $paginated){
        $query = UserAccount::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', '=', 1);
            break;
            case 'inactive':
                $query = $query->where('status', '=', 0)->withTrashed();
            break;
            case 'primary':
                $query = $query->where('primary_account', '=', 1);
            break;
            case 'user':
                $query = $query->where('user_id', '=', $specific);
            break;    
        }
        
        $query = $detailed ? $query->with('bank', 'user', 'creator', 'updater', 'deleter') : $query->select('id', 'account_number', 'account_name');
        $query = $query->orderBy('primary_account','DESC');
        $query = $paginated ? $query->paginate(50) : $query->get();
        return $query;
    }

    public function ums_user_account_get_by($type, $id, $specific){}

    public function ums_user_account_update($data, $id){
        DB::beginTransaction();
        try{
            $query = UserAccount::findOrFail($id);

            $query->bank_id = $data['bank_id'] ?? $query->bank_id;
            $query->user_id = $data['user_id'] ?? $query->user_id;
            $query->account_name = $data['account_name'] ?? $query->account_name;
            $query->account_number = $data['account_number'] ?? $query->account_number;
            $query->primary_account = $data['primary_account'] ?? $query->primary_account; 
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();   
            $this->log_user_activity('User Account Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Account Update', $id, false);
            return $e->getMessage();
        }
    }

    /*
    ----------------------------------------------------------------------------------
    User Next of Kin
    ----------------------------------------------------------------------------------
    */
    public function ums_user_next_of_kin_create($data, $user_id = null){
        DB::beginTransaction();

        try{

            $query = NextOfKin::create([
                'user_id' => $data['user_id'] ?? $user_id,
                'name' => $data['name'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'email' => $data['email'], 
                'relationship'=> $data['relationship'],
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);

            DB::commit();   
            $this->log_user_activity('User Next of Kin Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('User Next of Kin Create', null, false);
            return $e->getMessage();
        }
    }
}