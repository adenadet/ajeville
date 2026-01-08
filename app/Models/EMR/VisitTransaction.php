<?php

namespace App\Models\EMR;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitTransaction extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_visit_transactions';
    protected $fillable = array('date', 'visit_id', 'patient_id', 'service_type_id', 'item_name', 'item_id', 'serviceable_type', 'serviceable_id', 'quantity', 'unit_price', 'amount', 'billable', 'status','performed_at', 'performed_by', 'metadata', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'      
    );

    protected $casts = [
        'billable' => 'boolean',
        'metadata' => 'array',
        'performed_at' => 'datetime',
    ];

    const StatusPending     = 1;
    const StatusCompleted   = 100;
    const StatusCancelled   = 400;
    const StatusTransferred = 1000;
	
    public function coverage(){
    	return $this->hasOne('App\Models\EMR\VisitTransactionCoverage', 'visit_transaction_id', 'id');
	}

    public function item(){
    	return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
	}

    public function patient(){
    	return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
	}

    public function performer(){
    	return $this->belongsTo('App\Models\User', 'performed_by', 'id');
	}

    public function serviceable()
    {
        return $this->morphTo();
    }

    public function visit(){
    	return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
	}

    public function scopeBillable($query)
    {
        return $query->where('billable', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::StatusCompleted);
    }

    public function getOutstandingAmountAttribute()
    {
        $payable = $this->coverage &&
                $this->coverage->approval_status == VisitTransactionCoverage::ApprovalApproved
            ? $this->coverage->patient_payable
            : $this->amount;

        $paid = $this->paymentAllocations()->sum('amount');

        return max(0, $payable - $paid);
    }

}
