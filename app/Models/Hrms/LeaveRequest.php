<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Structure;

class LeaveRequest extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'hrms_leave_requests';
    protected $fillable = array('employee_id', 'user_leave_type_id', 'leave_type_id', 'leave_allowance', 'from_date', 'to_date', 'reason', 'remarks', 'status', 'is_half_day', 'leave_attachment', 'approved_by', 'approved_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function approver(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'approved_by', 'employee_id');
    }
    public function employee(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'employee_id', 'id');
    }

    public function employee_leave_type(){
        return $this->belongsTo('App\Models\Hrms\EmployeeLeaveType', 'user_leave_type_id', 'id');
    }

    public function leave_type(){
        return $this->belongsTo('App\Models\Hrms\LeaveType', 'leave_type_id', 'id');
    }
}
