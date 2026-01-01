<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Service extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_services';
    protected $fillable = array('id', 'item_id', 'service_type_id', 'reference_id', 'description', 'status', 'created_by', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');

    public function item(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function service_type(){
        return $this->belongsTo('App\Models\EMR\Settings\ServiceType', 'service_type_id', 'id');
    }
    
    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}