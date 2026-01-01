<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAllocation extends Model
{
    use HasFactory;

    const StatusDraft = 1;
    const StatusConfirmed = 10;
    const StatusReversed = 100;

    protected $primaryKey = 'id';
    protected $table = 'finance_payment_allocations';
    protected $fillable = array('payment_id', 'wallet_id', 'income_id', 'amount', 'date', 'created_at', 'updated_at', 'deleted_at');

    public function income(){
        return $this->belongsTo('App\Models\Finance\Income', 'income_id', 'id');
    }

    public function payment(){
        return $this->belongsTo('App\Models\Finance\Payment', 'payment_id', 'id');
    }
}
