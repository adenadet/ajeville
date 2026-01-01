<?php

namespace App\Models\EMR\Domiciliary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Assessment extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_assessments';
    protected $fillable = array('patient_id', 'domiciliary_id', 'assigned_by', 'assigned_date', 'status', 'assessed_by', 'assessed_at', 'approved_by', 'approved_at', 'approval_status', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');

    public function assessment_braden(){
    	return $this->belongsTo('App\Models\EMR\DomiciliaryAccessmentBraden', 'domiliciary_id', 'id');
	}

    public function domiciliary(){
        return $this->belongsTo('App\Models\EMR\Domiciliary', 'domiciliary_id', 'id');
    }
}