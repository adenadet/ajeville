<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_transactions';
    protected $fillable = array('date', 'visit_id', 'patient_id', 'item_id', 'service_type_id', 'item_name', 'item_qty', 'item_unit_cost', 'item_total', 'discount', 'description', 'status', 'service_status', 'paid_by', 'care_id', 'verified_by', 'created_by', 'updated_by', 'deleted_by', 'verified_at', 'created_at', 'updated_at', 'deleted_at');

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
        return $this->belongsTo('App\Models\Inventory\Classification', 'service_type_id', 'id');
    }

    public function visit(){
        return $this->belongsTo('App\Models\EMR\Visit', 'visit_id', 'id');
    }
}
