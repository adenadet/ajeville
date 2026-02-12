<?php

namespace App\Models\EMR\Anesthesia;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestCase extends Structure
{
    use HasFactory;

    const AnesthesiaTypeGeneral = 'general';
    const AnesthesiaTypeLocal = 'local';
    const AnesthesiaTypeRegional = 'regional';
    const AnesthesiaTypeSedation = 'sedation';
    
    
    const StatusRequested = 1;
    const StatusAssessed = 5;
    const StatusCleared = 20;
    const StatusInProgress = 40;
    const StatusCompleted = 100;
    const StatusSignedOff = 500;

    protected $primaryKey = 'id';
    protected $table = 'emr_anesthesia_cases';
    protected $fillable = array('visit_id', 'procedure_id', 'date', 'patient_id', 'anesthesia_type', 'asa_class', 'assigned_anesthetist_id', 'remarks', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function anesthetist(){
        return $this->belongsTo('App\Models\User', 'assigned_anesthestist_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function in_op(){
    	return $this->hasOne('App\Models\EMR\Anaesthia\InOp', 'id', 'case_id');
	}

    public function post_op(){
    	return $this->hasOne('App\Models\EMR\Anaesthia\PostOp', 'id', 'case_id');
	}

    public function pre_op(){
    	return $this->hasOne('App\Models\EMR\Anaesthia\PreOp', 'id', 'case_id');
	}

    public function procedure(){
    	return $this->belongsTo('App\Models\EMR\Procedure\Request', 'procedure_id', 'id');
	}

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

}

