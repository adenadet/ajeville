<?php

namespace App\Models\EMR;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitPayment extends Structure
{
    use HasFactory;

    const StatusConfirmed = 10;
    const StatusReceived    = 1;
    const StatusReversed    = 400;
    const StatusTransferred = 1000;

    const DefaultPaymentMethod = 0;

    protected $table = 'visit_payments';

    protected $fillable = [
        'visit_id', 'patient_id', 'amount', 'payment_method', 'reference', 'received_by', 'received_at', 'status', 'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function visit()
    {
        return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
    }

    public function patient()
    {
        return $this->belongsTo('App\Models\EMR\Patient\Patient', 'patient_id', 'id');
    }

    public function allocations()
    {
        return $this->hasMany('App\Models\EMR\VisitPaymentAllocation', 'visit_payment_id','id');
    }

    public function receiver()
    {
        return $this->belongsTo('App\Models\User', 'received_by', 'id');
    }

    public function getBalanceAttribute()
    {
        $allocated = $this->allocations()->sum('amount');

        return max(0, $this->amount - $allocated);
    }
}
