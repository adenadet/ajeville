<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'hrms_leave_types';
    protected $fillable = array('name', 'no_of_days', 'leave_category', 'status', 'recurring', 'date', 'start_date', 'end_date', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function employee(){
        return $this->belongsTo('App\Models\HRMS\Employee', 'employee_id', 'id');
    }

    public function leave_type(){
        return $this->belongsTo('App\Models\HRMS\LeaveType', 'leave_type_id', 'id');
    }
    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
