<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAccount extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'crm_customer_bank_accounts';
    protected $fillable = array('customer_id', 'bank_id', 'account_number', 'account_name', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function customer(){
    	return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
	}

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}
    
    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
	}

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
	}
}
