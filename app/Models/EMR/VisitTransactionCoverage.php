<?php

namespace App\Models\EMR;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitTransactionCoverage extends Structure
{
    use HasFactory;

    const ApprovalPending  = 'pending';
    const ApprovalApproved = 'approved';
    const ApprovalRejected = 'rejected';

    const ClaimOpen      = 1;
    const ClaimSubmitted = 10;
    const ClaimPaid      = 100;
    const ClaimRejected  = 4000;

    protected $table = 'emr_visit_transaction_coverages';

    protected $fillable = ['visit_transaction_id', 'provider_id', 'plan_id', 'authorization_code', 'covered_amount', 'patient_payable', 'coverage_percent', 'approval_status', 'claim_status', 'notes', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function plan(){
        return $this->belongsTo('App\Models\Insurance\Plan','plan_id','id');
    }

    public function provider(){
        return $this->belongsTo('App\Models\Insurance\Provider', 'provider_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function visit_transaction(){
        return $this->belongsTo('App\Models\EMR\VisitTransaction', 'visit_transaction_id', 'id');
    }

}
