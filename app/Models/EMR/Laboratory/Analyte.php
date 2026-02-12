<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analyte extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_laboratory_analytes';
    protected $fillable = array('name', 'default_unit', 'input_type', 'created_at', 'updated_at', 'deleted_at');

    public function reference_ranges()
    {
        return $this->hasMany('App\Models\EMR\Laboratory\ReferenceRange', 'analyte_id', 'id');
    }
}