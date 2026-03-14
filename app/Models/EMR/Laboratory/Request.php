<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Structure
{

    const StatusBooked = 0;
    const StatusAccepted = 1;
    const StatusStarted = 2;
    const StatusSampleCollected = 4;
    const StatusOngoing = 5;
    const StatusConfirmed = 20;
    const StatusCompleted = 30;
    const StatusCancelled = 100;

    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_requests';
    protected $fillable = array('unique_id', 'date', 'visit_id', 'branch_id', 'consultation_id', 'laboratory_service_id', 'patient_id', 'transaction_id', 'quantity', 'item_id', 'description', 'status', 'special', 'accepted_by', 'accepted_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function branch(){
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }

    public function canBeAccepted()
    {
        return $this->status === self::StatusBooked;
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function item(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function lab_service(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Service', 'laboratory_service_id', 'id');
    }
    public function patient(){
        return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
    }

    public function requester(){
        return $this->belongsTo('App\Models\User', 'requested_by', 'id');
    }
    
    public function result(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Result', 'id', 'request_id');
    }

    public function specimens(){
        return $this->hasMany('App\Models\EMR\Laboratory\Specimen', 'request_id', 'id');
    }
    
    public function transaction(){
        return $this->belongsTo('App\Models\EMR\VisitTransaction', 'transaction_id', 'id');
    }

    public function visit(){
        return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
    }
}
