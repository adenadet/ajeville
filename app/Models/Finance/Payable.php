<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Structure
{
    
    use HasFactory;

    const StatusConfirmed = 'confirmed';
    const StatusPending = 'pending';
    const StatusPartPayment = 'part';
    const StatusPaid = 'paid';

        protected $primaryKey = 'id';
    protected $table = 'finance_jornals';
    protected $fillable = array(
        'description', 'amount', 'transaction_type_id', 'cost_center_id', 'currency_id', 'date', 'reference_id', 'reference_type', 'confirmed_by', 'confirmed_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function collector(){
        return $this->belongsTo('App\Models\User', 'collected_by', 'id');
    }

    public function cost_center(){
        return $this->belongsTo('App\Models\Finance\CostCenter', 'cost_center_id', 'id');
    }

    public function currency(){
        return $this->belongsTo('App\Models\Finance\Currency', 'currency_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function reference(){
        switch ($this->reference_type)
        {
            case 'sales_order':
                return $this->belongsTo('App\Models\Sales\Order', 'reference_id', 'id');
            case 'purchase_order':
                return $this->belongsTo('App\Models\Procurement\PurchaseOrder', 'reference_id', 'id');
            case 'work_order':
                return $this->belongsTo('App\Models\Procurement\WorkOrder', 'reference_id', 'id');
        }
    }
}
