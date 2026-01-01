<?php

namespace App\Models\EMR\Consultation;

use App\Models\Structure;

class Consultation extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_consultations';
    protected $fillable = array('unique_id', 'patient_id', 'visit_id', 'specialty_id', 'transaction_id', 'consultation_type_id',  'whom_to_see', 'consultant_id', 'consultant_seen_id', 'initial_diagnosis', 'final_diagnosis', 'complaint', 'soap_note', 'action_plan', 'status', 'start_time', 'end_time', 'created_by', 'updated_by', 'deleted_by');

	protected $casts = [
		'start_time' => 'datetime',
		'end_time' => 'datetime',
	];

    public function consultant_seen(){
    	return $this->belongsTo('App\Models\Hrms\Employee', 'consultant_seen_id', 'id');
	}

	public function consultant(){
    	return $this->belongsTo('App\Models\Hrms\Employee', 'consultant_id', 'id');
	}

	public function consultation_type(){
		return $this->belongsTo('App\Models\Inventory\Item', 'consultation_type_id', 'id');
	}

	public function group(){
    	return $this->belongsTo('App\Models\EMR\Consultation\Group', 'whom_to_see', 'id');
	}

    public function hospital(){
    	return $this->belongsTo('App\Models\EMR\Hospital', 'hospital_id', 'id');
	}

    public function specialty(){
    	return $this->belongsTo('App\Models\EMR\Consultation\Specialty', 'specialty_id', 'id');
	}

    public function initial_codes(){
    	return $this->belongsTo('App\Models\EMR\Settings\ICD10', 'initial_diagnosis', 'id');
	}

	public function final_codes(){
    	return $this->belongsTo('App\Models\EMR\Settings\ICD10', 'final_diagnosis', 'id');
	}

	public function item(){
    	return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
	}

	public function laboratory(){
		return $this->hasMany('App\Models\EMR\Laboratory\Request', 'consultation_id', 'id');
	}

	public function patient(){
    	return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
	}

	public function prescriptions(){
		return $this->hasMany('App\Models\EMR\Pharmacy\Prescription', 'consultation_id', 'id');
	}

	public function radiology(){
		return $this->hasMany('App\Models\EMR\Radiology\Request', 'consultation_id', 'id');
	}

    public function transaction(){
    	return $this->belongsTo('App\Models\Finance\Transaction', 'transaction_id', 'id');
	}

	public function visit(){
    	return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
	}
}
