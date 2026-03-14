<?php

namespace App\Models\Insurance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    
    protected $primaryKey = 'id';
    protected $table = 'hmo_providers';
    protected $fillable = array('name', 'description', 'hmo_type_id', 'website', 'portal', 'phone', 'created_by', 'updated_by', 'contact_person', 'cp_phone', 'cp_email', 'status');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

    public function insurance_type(){
    	return $this->belongsTo('App\Models\Insurance\ProviderType', 'hmo_type_id', 'id');
	}

    public function plans(){
        return $this->hasMany('App\Models\Insurance\Plan', 'provider_id', 'id');
    }

    public function updator(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
	}


}
