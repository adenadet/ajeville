<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Structure;

class Appointment extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_appointments';
    protected $fillable = array('unique_id', 'branch_id', 'patient_id', 'care_id', 'whom_to_see_id', 'specialty_id', 'service_id', 'timestamp', 'visit_type_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

    public function branch(){
    	return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
	}
    
	public function consultant(){
    	return $this->belongsTo('App\Models\User', 'whom_to_see_id', 'id');
	}

	public function partner(){
		return $this->belongsTo('App\Models\Insurance\Plan', 'care_id', 'id');
	}
    public function patient(){
    	return $this->belongsTo('App\Models\EMR\Patient', 'patient_id', 'id');
	}

	public function price_list(){
		return $this->belongsTo('App\Models\Finance\PriceList', 'care_id', 'id');
	}

    public function transactions(){
    	return $this->hasMany('App\Models\Finance\Transaction', 'visit_id', 'id');
	}

    public function visit_type(){
    	return $this->belongsTo('App\Models\EMR\VisitType', 'visit_type_id', 'id');
	}

}
