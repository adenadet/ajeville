<?php

namespace App\Models\Insurance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authorization extends Structure
{
    public const  StatusRequested = 1;
    public const  StatusApproved = 10; 
    public const  StatusRejected = 100;
    public const  StatusExpired = 75;
    protected $primaryKey = 'id';
    protected $table = 'hmo_provider_authorizations';
    protected $fillable = array('provider_id', 'plan_id', 'patient_id', 'auth_code', 'requested_by', 'requested_at', 'approved_by', 'approved_at', 'valid_until', 'status', 'remarks', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function plan(){
    	return $this->belongsTo('App\Models\Insurance\Plan', 'plan_id', 'id');
	}

    public function provider(){
    	return $this->belongsTo('App\Models\Insurance\Provider', 'provider_id', 'id');
	}

    public function patient(){
        return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
    }
}
