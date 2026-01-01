<?php

namespace App\Models\Ums;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'customer_addresses';
    protected $fillable = array('user_id', 'type', 'street', 'street_2', 'city', 'state_id', 'area_id', 'status', 'confirmed_by', 'created_by', 'updated_by', 'deleted_by', 'confirmed_at', 'created_at', 'updated_at', 'deleted_at'); 

    public function customer(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function state(){
        return $this->belongsTo('App\Models\State', 'state_id', 'id');
    }

    public function area(){
        return $this->belongsTo('App\Models\Area', 'area_id', 'id');
    }

    public function verification(){
        return $this->belongsTo('App\Models\Ums\CustomerAddressVerification', 'id', 'address_id');
    }

    public function verifier(){
        return $this->belongsTo('App\Models\User', 'confirmed_by', 'id');
    }

}