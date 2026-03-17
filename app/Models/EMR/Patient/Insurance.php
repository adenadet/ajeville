<?php

namespace App\Models\EMR\Patient;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Structure
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'emr_patient_insurances';
    protected $fillable = array('patient_id', 'plan_id', 'enrollee_id', 'expiry_date', 'other_details', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function plan(){
    	return $this->belongsTo('App\Models\Insurance\Plan', 'plan_id', 'id');
	}

    public function patient(){
    	return $this->belongsTo('App\Models\EMR\Patient', 'patient_id', 'id');
	}
}