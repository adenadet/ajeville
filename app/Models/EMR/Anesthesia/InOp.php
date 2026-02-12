<?php

namespace App\Models\EMR\Anesthesia;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InOp extends Structure
{
    use HasFactory;

    const StatusInProgress = 10;
    const StatusCompleted = 100;
    
    protected $primaryKey = 'id';
    protected $table = 'emr_anesthesia_case_in_operations';
    protected $fillable = array('case_id', 'start_time', 'end_time', 'airway_device', 'ventilation_mode', 'remarks', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }
    
    public function drug_administrations(){
    	return $this->hasMany('App\Models\EMR\Anaesthia\DrugAdmin', 'case_id', 'case_id');
	}

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function vital_signs(){
    	return $this->hasMany('App\Models\EMR\Anaesthia\VitalSign', 'case_id', 'case_id');
	}
}