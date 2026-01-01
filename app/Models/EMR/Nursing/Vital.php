<?php

namespace App\Models\EMR\Nursing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vital extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_vitals';
    protected $fillable = array(
        'patient_id', 'consultation_id', 'visit_id', 'uuid', 'blood_glucose', 'bp_diastolic', 'bp_systolic', 'consciousness', 'gcs_eye', 'gcs_motor', 'gcs_verbal',  'glasgow_score', 'heart_beat', 'height', 'pain_on_movement', 'protein', 'pupil_left_reaction', 'pupil_left_size', 'pupil_right_reaction', 'pupil_right_size', 'pulse',  'remarks', 'respiration_rate', 'spo2', 'taken_by', 'temperature', 'urine', 'weight', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at'   
    );

    public function consultation(){
        return $this->belongsTo('App\Models\EMR\Consultation\Consultation', 'consultation_id', 'id');
    }

    public function creater(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function patient(){
        return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
    }

    public function taker(){
        return $this->belongsTo('App\Models\User', 'taken_by', 'id');
    }

    public function visit(){
        return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}