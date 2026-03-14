<?php
namespace App\Services\UMS;
use App\Http\Traits\General\FileTrait;
use App\Http\Traits\General\FileManagerTrait;
use App\Http\Traits\General\LogTrait;
use App\Mail\Ums\CompleteRegistrationMail;
use App\Models\User;

use App\Models\EMR\Patient;
use App\Models\HRMS\Employee;
use App\Models\Hrms\UserAccount;
use App\Models\NextOfKin;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;


class UserService{
    use FileManagerTrait, LogTrait;

    protected function actorId(): ?int
    {
        return auth('api')->id() ?? Auth::id();
    }

    public function add_next_of_kin($user_id, $data){
        return DB::transaction(function () use ($user_id, $data) {
            $query = NextOfKin::updateOrCreate([
                'user_id'   => $user_id,
            ],
            [
                'name' => $data['name'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'email' => $data['email'], 
                'relationship'=> $data['relationship'],
                'created_by' => $this->actorId(),
                'updated_by' => $this->actorId(),
            ]);
        });
    }

    public function change_password($user_id, $password){
        DB::beginTransaction();

        try{
            $user = User::findOrFail($user_id);
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
    public function create_user($data){
        $image_url = (!empty($data['image'])) ? $this->file_upload($data['image'], 'image', 'img/profile/', null) : 'default.png';

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

    public function create_temporary_user($data){
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
            //Mail::to($user->email)->send(new CompleteRegistrationMail($user));
        } 
        catch (Exception $e) {
            //Log::error('Mail sending failed: ' . $e->getMessage());
        }
        return $user;
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

    public function update_user($data, $id){
        DB::beginTransaction();
        try{
            $image_url = (!is_null($data['image']) && $data['image'] != 'default.png') ? $this->file_upload($data['image'], 'image', 'img/profile/', null) : 'default.png';
            
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
}