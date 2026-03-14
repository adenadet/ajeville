<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultVersion extends Model
{
    use HasFactory;

    public const StatusDraft = 0;
    public const StatusSubmitted = 10;
    public const StatusCompleted = 100;

    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_result_versions';
    protected $fillable = ['result_id', 'version_number', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function result()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Result', 'result_id');
    }

    public function values()
    {
        return $this->hasMany('App\Models\EMR\Laboratory\ResultValue', 'result_version_id', 'id');
    }
}
