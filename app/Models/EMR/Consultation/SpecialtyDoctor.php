<?php

namespace App\Models\EMR\Consultation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialtyDoctor extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_specialty_doctors';
    protected $fillable = array('specialty_id', 'doctor_id', 'created_at', 'updated_at', 'deleted_at');

    public function user(){
        return $this->belongsTo('App\Models\User', 'doctor_id', 'id');
    }

    public function specialty(){
        return $this->belongsTo('App\Models\EMR\Specialty', 'specialty_id', 'id');
    }
}
