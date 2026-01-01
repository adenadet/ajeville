<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vital extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_vitals';
    protected $fillable = array('patient_id', 'consultation_id', 'visit_id', 'systolic', 'diastolic', 'temperature', 'glucose', 'height', 'weight', 'heart_beat', 'spo2', 'respiratory', 'urine', 'protein', 'remarks', 'taken_by', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');
    
}
