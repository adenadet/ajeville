<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultVersion extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_result_versions';
    protected $fillable = ['result_id', 'old_value', 'new_value', 'changed_by', 'changed_at', 'created_at', 'updated_at', 'deleted_at'];

    public function result()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Result', 'result_id');
    }
}
