<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Employee extends Model
{
    use Notifiable;

    const StatusApplicant = 0;
    const StatusActive = 1;
    const StatusRetired = 5;
    const StatusResigned = 2;
    const StatusTerminated = 3;
    const StatusDeceased = 4;
    
    protected $primaryKey = 'id';
    protected $table = 'hrms_employees';
    protected $fillable = array('user_id', 'employee_id', 'office_shift_id', 'reports_to', 'supervisor_id', 'username', 'email', 'department_id', 'sub_department_id', 'designation_id', 'date_of_joining', 'date_of_leaving', 'employment_status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function department(){
        return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    public function designation(){
        return $this->belongsTo('App\Models\Hrms\Designation', 'designation_id', 'id');
    }

    public function leave_types(){
        return $this->hasMany('App\Models\Hrms\EmployeeLeaveType', 'employee_id', 'id');
    }
    public function line_manager(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'reports_to', 'employee_id');
    }

    public function supervisor(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'supervisor_id', 'employee_id');
    }
    
    public function team(){
        return $this->belongsTo('App\Models\Hrms\Team', 'team_id', 'team_id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }   
}
