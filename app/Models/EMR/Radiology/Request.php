<?php

namespace App\Models\EMR\Radiology;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Structure
{

    const StatusBooked = 0;
    const StatusStarted = 1;
    const StatusSampleCollected = 2;
    const StatusReferredOut = 5;
    const StatusReported = 10;
    const StatusSecondaryReport = 15;
    const StatusConfirmed = 20;
    
    protected $primaryKey = 'id';
    protected $table = 'emr_radiology_requests';
    protected $fillable = array('visit_id', 'date', 'patient_id', 'item_id', 'consultation_id', 'transaction_id', 'branch_id', 'quantity', 'status', 'result', 'sample_by', 'sample_at', 'sample_remark', 'reported_by', 'reported_at', 'report_remark', 'secondary_report_by', 'secondary_report_at', 'secondary_report_remark', 'approved_by', 'approved_at', 'approval_remark', 'outsourced_by', 'outsourced_type', 'outsourced_to_id', 'outsourced_status_id', 'outsourced_remark', 'insourced_remark', 'insourced_final_remark', 'outsource_result_file', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function approver(){
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }
    
    public function collector(){
        return $this->belongsTo('App\Models\User', 'sample_collected_by', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function patient(){
        return $this->belongsTo('App\Models\EMR\Patient', 'patient_id', 'id');
    }

    public function test(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function reporter(){
        return $this->belongsTo('App\Models\User', 'reported_by', 'id');
    }
    
    public function secondary_reporter(){
        return $this->belongsTo('App\Models\User', 'secondary_reported_by', 'id');
    }
    
    public function transaction(){
        return $this->belongsTo('App\Models\Finance\Transaction', 'transaction_id', 'id');
    }
}
