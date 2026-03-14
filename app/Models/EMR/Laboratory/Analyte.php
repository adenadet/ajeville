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

    public function resolveReferenceRange($age, $gender)
    {
        return $this->reference_ranges()
            ->where(function($q) use ($gender){
                $q->where('gender', $gender)->orWhere('gender', '=', 'Any');
            })->where('age_min','<=',$age)->where('age_max','>=',$age)->first();
    }
}