<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    public const StatusPending = 0;
    public const StatusEntered = 10;
    public const StatusAmended = 20;
    public const StatusSecondaryReview = 25;
    public const StatusVerified = 30;
    public const StatusReleased = 40;

    protected $table = 'emr_laboratory_results';

    protected $fillable = ['request_id', 'service_id', 'status', 'verified_by','entered_by','entered_at', 'verified_by', 'verified_at', 'released_by', 'released_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function analyte()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Analyte', 'analyte_id', 'id');
    }

    public function files()
    {
        return $this->hasMany('App\Models\EMR\Laboratory\ResultFile', 'id', 'request_id');
    }

    public function latestVersion()
    {
        return $this->hasOne('App\Models\EMR\Laboratory\ResultVersion', 'result_id', 'id')->latestOfMany();
    }

    public function request()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Request', 'request_id', 'id');
    }

    public function specimen()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\Specimen', 'specimen_id', 'id');
    }

    public function versions(){
        return $this->hasMany('App\Models\EMR\Laboratory\ResultVersion', 'result_id', 'id');
    }

    public function reviews(){
        return $this->hasMany('App\Models\EMR\Laboratory\ResultReview', 'result_id');
    }
}