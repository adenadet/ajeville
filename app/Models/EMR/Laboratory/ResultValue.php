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
    protected $fillable = ['result_id', 'analyte_id', 'value', 'unit', 'reference_range', 'flag', 'comment', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function result()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Result', 'result_id');
    }
}
