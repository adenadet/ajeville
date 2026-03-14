<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultValue extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_result_values';
    protected $fillable = ['result_version_id', 'analyte_id', 'specimen_id', 'value', 'unit', 'reference', 'reference_range', 'flag', 'comment', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'reference_range' => 'array'
    ];
    
    public function analyte()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Analyte','analyte_id');
    }

    public function resultVersion()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\ResultVersion','result_version_id');
    }

    public function specimen()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Specimen','specimen_id');
    }
}
