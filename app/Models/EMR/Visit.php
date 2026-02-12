<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Visit extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_visits';
    protected $fillable = array('unique_id', 'branch_id', 'patient_id', 'plan_id', 'status', 'start_date', 'start_timestamp', 'end_date', 'end_timestamp', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

	public const StatusBooked = 0;
	public const StatusOpen = 1; //Same as created
	public const StatusOngoing = 5; //Same as in progress
	public const StatusAwaitingBilling = 50; //Awaiting payment confirmation can be closed especially admissions
	public const StatusClosed = 100;
	public const StatusCancelled = 400;
	
    public function branch(){
    	return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
	}

	public function plan(){
		return $this->belongsTo('App\Models\Insurance\Plan', 'care_id', 'id');
	}
    public function patient(){
    	return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
	}

	public function price_list(){
		return $this->belongsTo('App\Models\Finance\PriceList', 'care_id', 'id');
	}

	public function mainTransaction()
    {
        return $this->morphOne('App\Models\Finance\MainTransaction','transactionable');
    }

    public function transactions(){
    	return $this->hasMany('App\Models\EMR\VisitTransaction', 'visit_id', 'id');
	}

    public function visit_type(){
    	return $this->belongsTo('App\Models\EMR\ServiceType', 'visit_type_id', 'id');
	}

}
