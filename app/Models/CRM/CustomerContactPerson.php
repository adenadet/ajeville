<?php

namespace App\Models\CRM;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerContactPerson extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'crm_customer_contact_persons';
    protected $fillable = array('customer_id', 'first_name', 'last_name', 'title', 'email', 'phone', 'alt_phone', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function customer(){
    	return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
    }
    
    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

}
