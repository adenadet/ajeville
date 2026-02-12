<?php

namespace App\Models\EMR\Anesthesia;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSign extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_anesthesia_case_in_operation_vital_signs';
    protected $fillable = array('case_id', 'time', 'blood_pressure', 'pulse', 'spo2', 'ecg', 'etco2', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function request_case(){
        return $this->belongsTo('App\Models\EMR\Anesthesia\RequestCase', 'case_id', 'id');
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