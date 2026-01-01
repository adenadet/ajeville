<?php

namespace App\Models\EMR\Pharmacy;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Model;

class PrescriptionDrug extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_prescription_drugs';
    protected $fillable = array('prescription_id', 'drug_id', 'specific_drug_id', 'drug_name', 'detail', 'dose', 'duration', 'frequency', 'form',  'route', 'quantity', 'start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at');

    public function drug(){
    	return $this->belongsTo('App\Models\EMR\Drug', 'drug_id', 'id');
	}

    public function route(){
    	return $this->belongsTo('App\Models\EMR\DrugRoute', 'route_id', 'id');
	}

    public function specific_drug(){
    	return $this->belongsTo('App\Models\Inventory\Item', 'specific_drug_id', 'id');
	}

    public function specifics(){
    	return $this->hasMany('App\Models\EMR\PrescriptionDrugSpecific', 'prescription_specific_drug_id', 'id');
	}
}
 