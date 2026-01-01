<?php

namespace App\Models\EMR\Drugs;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class 
Chart extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_nursing_drug_charts';

    protected $fillable = array('patient_id', 'prescription_id', 'status', 'started_at', 'notes', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

}