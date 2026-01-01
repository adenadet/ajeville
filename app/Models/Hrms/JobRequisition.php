<?php

namespace App\Models\Hrms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRequisition extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'hrms_job_requisitions';
    protected $fillable = [
        'department_id', 'requester_id', 'title', 'description', 'positions', 'status', 'justification', 'approved_at', 'approver_id', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];
    protected $casts = ['justification' => 'array','approved_at' => 'datetime'];

    public function department(){
        return $this->belongsTo('App/Models/Operations/Department');
    }
    public function requester(){
        return $this->belongsTo('App/Models/User','requester_id');
    }
    public function approver(){
        return $this->belongsTo('App/Models/User','approver_id');
    }
    public function postings() {
        return $this->hasMany('App/Models/Hrms/Job', );
    }

}
