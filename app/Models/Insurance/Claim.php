<?php

namespace App\Models\Insurance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Structure
{
    public const StatusDraft = 0;
    public const StatusSubmitted = 1;
    public const StatusApproved = 2;
    public const StatusPartiallyApproved = 3;
    public const StatusRejected = 4;
    public const StatusPartiallyPaid = 5;
    public const StatusPaid = 6;

    protected $primaryKey = 'id';
    protected $table = 'hmo_provider_claims';
    protected $fillable = array('provider_id', 'plan_id', 'patient_id', 'visit_id', 'claim_number', 'total_billed', 'total_covered', 'total_patient_portion', 'approved_amount', 'rejected_amount', 'paid_amount', 'status', 'submitted_by',  'submitted_at', 'approved_by', 'approved_at', 'paid_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function patient(){
        return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
    }

    public function plan(){
    	return $this->belongsTo('App\Models\Insurance\Plan', 'plan_id', 'id');
	}

    public function provider(){
    	return $this->belongsTo('App\Models\Insurance\Provider', 'provider_id', 'id');
	}

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}