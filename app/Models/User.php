<?php
namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\LarashopAdminResetPassword as ResetPasswordNotification;
use Carbon\Carbon;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use DB;
class User extends Authenticatable

{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    public $guard_name = 'web';

    protected $fillable = [
        'user_type', 'nation_id', 'first_name', 'middle_name', 'last_name', 'name', 'unique_id', 'branch_id', 'image', 'sex', 'street', 'street2', 'city', 'area_id', 'state_id', 'department_id', 'phone', 'alt_phone', 'dob', 'joined_at', 'email', 'personal_email', 'marital_status', 'password', 'remember_token', 'created_at', 'updated_at', 'deleted_by', 'deleted_at'
    ];

    public function applicant(){
        return $this->belongsTo('App\Models\Hrms\Applicant', 'id', 'user_id');
    }
     
    public function area(){
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }
          
    public function branch(){
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function branches(){
        return $this->hasManyThrough('App\Models\Operations\Branch', 'App\Models\UserBranch', 'user_id', 'id', 'id', 'branch_id');
    }

    public function company(){
        return $this->belongsTo('App\Models\Ums\Company', 'user_id', 'id');
    }

    public function department(){
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }
    
    public function employee(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'id', 'user_id');
    }

    public function loans(){
        return $this->hasMany('App\Models\Loan', 'user_id', 'id');
    }

    public function modules(){
        return $this->hasManyThrough('App\Models\Operations\Module', 'App\Models\Operations\ModuleUser','id', 'id', 'module_id','user_id',  );        
    }

    public function next_of_kin(){
        return $this->belongsTo('App\Models\NextOfKin', 'id', 'user_id');        
    }

    public function patient(){
        return $this->belongsTo('App\Models\EMR\Patient', 'id', 'user_id');
    }

    
    public function repayments(){
        return $this->hasMany('App\Models\Repayment', 'user_id', 'id');
    }

    public function state(){
        return $this->belongsTo('App\Models\State', 'state_id', 'id');        
    }

    public function scopeBirthDayBetween($query, Carbon $from, Carbon $till) {
        $fromMonthDay = $from->format('m-d');
        $tillMonthDay = $till->format('m-d');
        if ($fromMonthDay <= $tillMonthDay) {
            //normal search within the one year
            $query -> whereRaw("DATE_FORMAT(dob, '%m-%d') BETWEEN '{$fromMonthDay}' AND '{$tillMonthDay}'");
        } 
        else {
            //we are overlapping a year, search at end and beginning of year
            $query->where(function($query) use($fromMonthDay, $tillMonthDay) {
                $query -> whereRaw("DATE_FORMAT(dob, '%m-%d') BETWEEN '{$fromMonthDay}' AND '12-31'")
                ->orWhereRaw("DATE_FORMAT(dob, '%m-%d') BETWEEN '01-01' AND '{$tillMonthDay}'");
            });
        }
    }

    public function user_roles(){
        return $this->hasMany('App\Models\Ums\Role', 'id', 'model_id');
    }

    protected $hidden = [
        'password', 'remember_token', 'pin'
    ];
}