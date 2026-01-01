<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment  extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'finance_payments';
    protected $fillable = array('date', 'customer_id', 'amount', 'mode_id', 'bank_id', 'status', 'collected_by', 'collected_at', 'confirmed_by', 'confirmed_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function account(){
    	return $this->belongsTo('App\Models\Finance\BranchBank', 'bank_id', 'id');
	}
	
	public function customer(){
    	return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
	}

	public function repayment(){
    	return $this->belongsTo('App\Models\Repayment', 'ref_id', 'id');
	}

	public function contribution(){
    	return $this->belongsTo('App\Models\Contribution', 'ref_id', 'id');
	}

	public function confirmed_by(){
    	return $this->belongsTo('App\Models\User', 'admin_id', 'id');
	}

	public function paid_to(){
    	return $this->belongsTo('App\Models\User', 'trans_admin_id', 'id');
	}
}