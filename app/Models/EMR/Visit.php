<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Visit extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_visits';
    protected $fillable = array('unique_id', 'branch_id', 'patient_id', 'care_id', 'status', 'start_date', 'start_timestamp', 'end_date', 'end_timestamp', 'visit_type_id', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

	public const StatusPending = 0;
	public const StatusAdmitted = 5;
	public const StatusStarted = 1;
	public const StatusEnd = 100;
	
    public function branch(){
    	return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
	}
	public function consultant(){
    	return $this->belongsTo('App\Models\User', 'patient_id', 'id');
	}

	public function partner(){
		return $this->belongsTo('App\Models\Insurance\Plan', 'care_id', 'id');
	}
    public function patient(){
    	return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
	}

	public function price_list(){
		return $this->belongsTo('App\Models\Finance\PriceList', 'care_id', 'id');
	}

    public function transactions(){
    	return $this->hasMany('App\Models\Finance\Transaction', 'visit_id', 'id');
	}

    public function visit_type(){
    	return $this->belongsTo('App\Models\EMR\ServiceType', 'visit_type_id', 'id');
	}

}
