<?php

namespace App\Models\EMR\Patient;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Structure
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'emr_patients';
    protected $fillable = array('user_id', 'balance', 'credit_limit', 'patient_type', 'unique_id', 'old_emr_numbers', 'blood_group', 'genotype', 'occupation', 'referral_type_id', 'referral_details', 'other_details', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');

	public const TypeTemp = 2;
	public const TypeReg = 1;
	
    public function allergies(){
    	return $this->hasMany('App\Models\EMR\Patient\Allergy', 'patient_id', 'id');
	}

    public function contacts(){
    	return $this->hasMany('App\Models\EMR\Patient\Contact', 'patient_id', 'id');
	}

	public function insurances(){
		return $this->hasMany('App\Models\EMR\Patient\Insurance', 'patient_id', 'id');
	}
    
	public function transactions(){
    	return $this->hasMany('App\Models\Finance\Transaction', 'patient_id', 'id');
	}
    
    public function user(){
    	return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}
}
