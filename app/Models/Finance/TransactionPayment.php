<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_transaction_payments';
    protected $fillable = array('transaction_id', 'source', 'plan_id', 'amount', 'auth_code', 'auth_channel', 'auth_description', 'auth_personnel', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    public function item(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function patient(){
        return $this->belongsTo('App\Models\EMR\Patient', 'patient_id', 'id');
    }

    public function payments(){
        return $this->hasMany('App\Models\Finance\TransactionPayment', 'transaction_id', 'id');
    }

    public function service_type(){
        return $this->belongsTo('App\Models\EMR\Service', 'service_type_id', 'id');
    }

    public function visit(){
        return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
    }
}
