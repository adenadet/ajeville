<?php

namespace App\Models\Insurance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderType extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'hmo_provider_types';
    protected $fillable = array('name', 'deleted_at');


    public function providers(){
    	return $this->hasMany('App\Models\Insurance\Provider', 'hmo_type_id', 'id',);
	}

    public function plans(){
        return $this->hasManyThrough('App\Models\Insurance\Plan', 'App\Models\Insurance\Provider', 'hmo_type_id', 'provider_id', 'id', 'id');
    }
}
