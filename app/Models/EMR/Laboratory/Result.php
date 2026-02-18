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
    public const StatusVerified = 30;
    public const StatusReleased = 40;

    protected $table = 'emr_laboratory_results';

    protected $fillable = ['request_item_id', 'status', 'entered_by', 'verified_by', 'released_by', 'entered_at', 'verified_at', 'released_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function request_item()
    {
        return $this->belongsTo('App\Models\EMR\Laboratory\RequestItem', 'request_item_id');
    }

    public function values(){
        return $this->hasMany('App\Models\EMR\Laboratory\ResultValue', 'result_id');
    }

    public function reviews(){
        return $this->hasMany('App\Models\EMR\Laboratory\ResultReview', 'result_id');
    }
}
