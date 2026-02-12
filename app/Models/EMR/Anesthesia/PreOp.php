<?php

namespace App\Models\EMR\Anesthesia;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOp extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_anesthesia_case_pre_operations';
    protected $fillable = array('case_id', 'accessed_by', 'date', 'airway_score', 'risk_notes', 'fitness', 'recommendations', 'planned_anaesthia_type', 'consent_obtained', 'consent_id', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function accessor(){
        return $this->belongsTo('App\Models\User', 'accessed_by', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
