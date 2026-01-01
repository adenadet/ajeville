<?php

namespace App\Models\Procurement;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'procurement_vendors';
    protected $fillable = array( 'name', 'address', 'balance', 'phone', 'email', 'category_id', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function accounts(){
        return $this->hasMany('App\Models\Procurement\VendorAccount', 'vendor_id', 'id');
    }

    public function category(){
        return $this->belongsTo('App\Models\Procurement\VendorCategory', 'category_id', 'id');
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
