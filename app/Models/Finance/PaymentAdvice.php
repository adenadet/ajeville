<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAdvice extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_vendor_payment_advices';
    protected $fillable = array('date', 'transaction_id', 'source', 'plan_id', 'amount', 'auth_code', 'auth_channel', 'auth_description', 'auth_personnel', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
