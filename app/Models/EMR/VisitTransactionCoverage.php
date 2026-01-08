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

    protected $table = 'visit_transaction_coverages';

    protected $fillable = ['visit_transaction_id', 'provider_id', 'plan_id', 'authorization_code', 'covered_amount', 'patient_payable', 'coverage_percent', 'approval_status', 'claim_status', 'notes',];

    public function visit_transaction()
    {
        return $this->belongsTo('App\Models\EMR\VisitTransaction', 'visit_transaction_id', 'id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Insurance\Provider', 'provider_id', 'id');
    }

    public function plan()
    {
        return $this->belongsTo('App\Models\Insurance\Plan','plan_id','id');
    }
}
