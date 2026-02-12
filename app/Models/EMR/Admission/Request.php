<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use App\Models\Ticket\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Structure
{
    public const StatusPending = 0;    
    public const StatusDraft = 0;
    public const StatusConfirmed = 1;
    public const StatusPrechecked = 2;
    public const StatusBedAssigned = 3;
    public const StatusBilled = 4;
    public const StatusAdmitted = 10;
    public const StatusDischarged = 20;
    public const StatusDeleted = 100;

    protected $primaryKey = 'id';
    protected $table = 'emr_admission_requests';
    protected $fillable = array('date', 'visit_id', 'branch_id', 'consultation_id', 'patient_id', 'admission_type_id', 'admission_reason', 'confirmed_by', 'confirmed_at', 'admitted_by', 'admission_note', 'admitted_date', 'admitted_at', 'discharged_by', 'discharged_at', 'requested_by', 'requested_at', 'requested_remark', 'bed_assigned_by', 'bed_assigned_at','precheck_by', 'precheck_at', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function admission_type(){
		    return $this->belongsTo('App\Models\EMR\Admission\Type', 'admission_type_id', 'id');
    }

    public function bed_assignment(){
        return $this->belongsTo('App\Models\EMR\Admission\BedAssignment', 'bed_assignment_id', 'id');
    }

    public function creator(){
    	  return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function patient(){
    	  return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
    }

    public function pre_admission_checks(){
        return $this->hasMany('App\Models\EMR\Admission\PreAdmissionCheck', 'admission_id', 'id');
    }

    public function updater(){
    	  return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function visit(){
    	  return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
    }
}
