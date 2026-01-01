<?php

namespace App\Models\Hrms;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAllowance extends Structure
{
    const StatusPending = 0; const StatusProcessed = 1; const StatusApproved = 2; const StatusPaid = 3; const StatusRejected = 9;
    protected $primaryKey = 'id';
    protected $table = 'hrms_leave_allowances';
    protected $fillable = array('employee_id', 'request_id', 'status', 'amount', 'processed_by', 'processor_remark', 'processed_at', 'approved_by', 'approved_at', 'approval_remark', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

    public function approver(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'approved_by', 'employee_id');
    }

    public function employee(){
        return $this->belongsTo('App\Models\Hrms\Employee', 'employee_id', 'id');
    }

    public function employee_leave(){
        return $this->belongsTo('App\Models\Hrms\LeaveRequest', 'request_id', 'id');
    }
}
