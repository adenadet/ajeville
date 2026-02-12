<?php

namespace App\Models\EMR\Anesthesia;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugAdmin extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_anesthesia_case_in_operation_drug_administrations';
    protected $fillable = array('case_id', 'drug_id', 'route_id', 'dose', 'quantity', 'form_id', 'time', 'remarks', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function drug(){
    	return $this->belongsTo('App\Models\EMR\Drug\Drug', 'drug_id', 'id');
	}

    public function route(){
    	return $this->belongsTo('App\Models\EMR\Drug\Route', 'route_id', 'id');
	}
}