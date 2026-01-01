<?php

namespace App\Models\Finance;

use App\Models\Structure;

class Payment extends Structure
{
    const StatusUnconfirmed = 0;
    const StatusConfirmed = 100;
    const StatusReversed = 400;
    
    protected $primaryKey = 'id';
    protected $table = 'finance_payments';
    protected $fillable = array('date', 'customer_id', 'vendor_id', 'staff_id', 'income_id', 'amount', 'mode_id', 'bank_id', 'description', 'status', 'collected_by', 'collected_at', 'confirmed_by', 'confirmed_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function account(){
    	return $this->belongsTo('App\Models\Finance\BranchBank', 'bank_id', 'id');
	}

    public function allocations(){
        return $this->hasMany('App\Models\Finance\PaymentAllocation', 'payment_id', 'id');
    }
	
	public function customer(){
    	return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
	}

    public function mode(){
        return $this->belongsTo('App\Models\Finance\PaymentMode', 'mode_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function collector(){
        return $this->belongsTo('App\Models\User', 'collected_by', 'id');
    }

    public function confirmer(){
        return $this->belongsTo('App\Models\User', 'confirmed_by', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

}
