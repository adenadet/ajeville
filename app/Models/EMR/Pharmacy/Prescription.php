<?php

namespace App\Models\EMR\Pharmacy;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Prescription extends Structure
{

    public const StatusBooked = 0;
    public const StatusCancelled = 100;
    public const StatusConfirmed = 10;

    protected $primaryKey = 'id';
    protected $table = 'emr_prescriptions';
    protected $fillable = array('visit_id', 'consultation_id', 'patient_id', 'date', 'doctor_id', 'doctor_name', 'refill_count', 'valid_till', 'status', 'start_date', 'end_date', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function consultation(){
    	return $this->belongsTo('App\Models\EMR\Consultation', 'consultation_id', 'id');
	}

    public function doctor(){
    	return $this->belongsTo('App\Models\HRMS\Employee', 'doctor_id', 'id');
	}

    public function prescription_drugs(){
        return $this->hasMany('App\Models\EMR\PrescriptionDrug', 'prescription_id', 'id');
    }

    public function patient(){
    	return $this->belongsTo('App\Models\User', 'patient_id', 'id');
	}

    public function visit(){
    	return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
	}

    
    
}
