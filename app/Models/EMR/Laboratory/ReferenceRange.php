<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceRange extends Structure
{
    use HasFactory;

    protected $table = 'emr_settings_laboratory_reference_ranges';

    protected $fillable = ['analyte_id', 'gender', 'age_min', 'age_max', 'low_value', 'normal_value', 'high_value', 'critical_low', 'created_by', 'updated_by', 'deleted_by'];

    public function analyte()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Analyte', 'analyte_id', 'id');
    }
}
