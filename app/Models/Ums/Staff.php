<?php
namespace App\Models\Ums;

use App\Models\Structure;
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
class Staff extends Structure 
{
    use HasApiTokens, Notifiable, HasRoles, SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'staffs';

    protected $fillable = [
        'user_id', 'unique_id', 'branch_id', 'image', 'sex', 'street', 'street2', 'city', 'area_id', 'state_id', 'nation_id', 'department_id', 'phone', 'alt_phone', 'dob', 'joined_at', 'email', 'personal_email', 'marital_status', 'created_at', 'updated_at', 'deleted_by', 'deleted_at'  
        ];

    public function area(){
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }
          
    public function branch(){
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }
    
    public function department(){
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    } 
    
    public function state(){
        return $this->belongsTo('App\Models\State', 'state_id', 'id');        
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
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

    protected $hidden = [
        'password', 'remember_token', 'pin'
    ];

}

