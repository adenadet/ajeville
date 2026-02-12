<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultValue extends Model
{
    use HasFactory;

    protected $table = 'emr_laboratory_result_values';

    protected $fillable = ['result_id', 'analyte_name', 'value', 'unit', 'reference_range', 'flag', 'comment', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function result()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Result', 'result_id');
    }
}
