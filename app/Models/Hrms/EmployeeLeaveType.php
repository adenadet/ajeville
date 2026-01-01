<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'hrms_employee_leave_types';
    protected $fillable = array('employee_id', 'leave_type_id', 'balance', 'days_used', 'pending_days', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function employee(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'employee_id', 'id');
    }

    public function leave_type(){
        return $this->belongsTo('App\Models\Hrms\LeaveType', 'leave_type_id', 'id');
    }
}
