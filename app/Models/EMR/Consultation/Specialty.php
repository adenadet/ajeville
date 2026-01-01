<?php

namespace App\Models\EMR\Consultation;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_specialties';
    protected $fillable = array('name', 'created_at', 'updated_at', 'deleted_at');

    public function doctors(){
    	return $this->HasMany('App\Models\EMR\Consultation\SpecialtyDoctor',  'specialty_id', 'id',); 
	}
}
