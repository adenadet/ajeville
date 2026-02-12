<?php

namespace App\Models\EMR;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitPaymentAllocation extends Structure
{
    use HasFactory;

    protected $table = 'visit_payment_allocations';

    protected $fillable = ['visit_id', 'visit_payment_id', 'visit_transaction_id', 'amount', 'created_at', 'updated_at', 'deleted_at',];

    public function payment()
    {
        return $this->belongsTo('App\Models\EMR\VisitPayment', 'visit_payment_id', 'id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Models\EMR\VisitTransaction', 'visit_transaction_id', 'id');
    }
}
