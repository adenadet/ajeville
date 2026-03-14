<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    
    use HasFactory;
    public const StatusPending = 0;

    public const StatusAccepted = 10;
    
    public const StatusSampleCollected = 20;
    public const StatusOutsourced = 70;
    public const StatusOngoing = 30;
    public const StatusPendingVerification = 40;
    public const StatusVerified = 50;
    public const StatusReleased = 60;
    public const StatusAwaitingOutsourceResult = 80;
    public const StatusCancelled = 100;
    public const StatusSecondaryReport = 15;
    
    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_request_items';

    protected $fillable = ['request_id', 'service_id', 'status', 'priority', 'accepted_by', 'accepted_at', 'cancelled_by', 'cancelled_at', 'cancellation_reason', 'panel_id', 'category_id', 'is_outsourced', 'outsource_order_id', 'outsource_type', 'linked_request_detail_id', 'analysis_started_at','analysis_completed_at', 'status', 'tat_minutes', 'verified_by', 'verified_at', 'released_by', 'released_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');    
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');    
    }

    public function request(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Request', 'request_id', 'id');
    }

    public function service(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Service', 'service_id', 'id');
    }

    public function specimen(){
        return $this->hasMany('App\Models\EMR\Laboratory\Specimen', 'request_item_id', 'id');
    }

    public function result(){
        return $this->hasOne('App\Models\EMR\Laboratory\Result', 'request_item_id', 'id');
    }

    public function queueEntries(){
        return $this->hasMany('App\Models\EMR\Laboratory\Queue', 'request_item_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');    
    }
}
