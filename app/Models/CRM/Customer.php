<?php

namespace App\Models\CRM;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'crm_customers';
    protected $fillable = array('uuid', 'name', 'balance', 'address', 'delivery_address', 'phone', 'email', 'category_id', 'tin', 'vatable', 'withholding_tax', 'website', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function category(){
    	return $this->belongsTo('App\Models\CRM\CustomerCategory', 'category_id', 'id');
	}

    public function contacts(){
    	return $this->hasMany('App\Models\CRM\CustomerContactPerson', 'customer_id', 'uuid');
	}

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}
    
    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
	}

    public function orders(){
    	return $this->hasMany('App\Models\Sales\Order', 'customer_id', 'uuid');
	}

    public function payments(){
    	return $this->hasMany('App\Models\Finance\Payment', 'customer_id', 'uuid');
	}
    
    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
	}
}
