<?php

namespace App\Models\EMR\Drugs;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class ChartDispense extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_nursing_drug_chart_dispenses';

    protected $fillable = array('id', 'drug_id', 'drug_chart_id', 'notes', 'dispensed_by', 'dispensed_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

}