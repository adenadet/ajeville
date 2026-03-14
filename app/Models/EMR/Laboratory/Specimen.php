<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specimen extends Structure
{
    use HasFactory;

    public const StatusPendingCollection = 0;
    public const StatusCollected = 10;
    public const StatusReceived = 20;
    public const StatusRejected = 30;
    public const StatusSentOut = 40;
    public const StatusReturned = 50;
    
    protected $table = 'emr_laboratory_specimens';

    protected $fillable = ['request_id', 'unique_id', 'barcode', 'bottle_type_id', 'specimen_type_id', 'status', 'collected_by', 'collected_at', 'received_by', 'received_at', 'received_remark', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function bottle(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Bottle', 'bottle_type_id', 'id');
    }

    public function collector(){
        return $this->belongsTo('App\Models\User', 'collected_by', 'id');
    }

    public function receiver(){
        return $this->belongsTo('App\Models\User', 'received_by', 'id');
    }

    public function request(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Request', 'request_id', 'id');
    }

    public function rejection(){
        return $this->hasOne('App\Models\EMR\Laboratory\SpecimenRejection', 'specimen_id', 'id');
    }

    public function specimen_type(){
        return $this->belongsTo('App\Models\EMR\Laboratory\SpecimenType', 'specimen_type_id', 'id');
    }

}
