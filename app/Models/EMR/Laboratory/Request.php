<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Structure
{

    const StatusBooked = 0;
    const StatusStarted = 1;
    const StatusSampleCollected = 2;
    const StatusOngoing = 4;
    const StatusConfirmed = 20;
    const StatusCancelled = 100;

    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_requests';
    protected $fillable = array('visit_id', 'date', 'patient_id', 'item_id', 'consultation_id', 'transaction_id', 'branch_id', 'quantity', 'status', 'result', 'sample_by', 'sample_at', 'sample_remark', 'reported_by', 'reported_at', 'report_remark', 'secondary_report_by', 'secondary_report_at', 'secondary_report_remark', 'approved_by', 'approved_at', 'approval_remark', 'outsourced_type', 'outsourced_to_id', 'outsourced_status_id', 'outsourced_remark', 'insourced_remark', 'insourced_final_remark', 'outsource_result_file', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function approver(){
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }
    
    public function branch(){
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }
    
    public function collector(){
        return $this->belongsTo('App\Models\User', 'sample_collected_by', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function item(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function patient(){
        return $this->belongsTo('App\Models\EMR\Patient', 'patient_id', 'id');
    }

    public function reporter(){
        return $this->belongsTo('App\Models\User', 'reported_by', 'id');
    }

    public function requester(){
        return $this->belongsTo('App\Models\User', 'requested_by', 'id');
    }
    
    public function secondary_reporter(){
        return $this->belongsTo('App\Models\User', 'secondary_reported_by', 'id');
    }

    public function tests(){
        return $this->hasMany('App\Models\EMR\LaboratoryRequestDetail', 'request_id', 'id');
    }
    
    public function transaction(){
        return $this->belongsTo('App\Models\Finance\Transaction', 'transaction_id', 'id');
    }

    public function visit(){
        return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
    }
}
