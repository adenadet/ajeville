<?php

namespace App\Models\Procurement;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorContactPerson extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'procurement_vendor_contact_persons';
    protected $fillable = array('vendor_id', 'first_name', 'last_name', 'title', 'email', 'phone', 'alt_phone', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function vendor(){
    	return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }
}
