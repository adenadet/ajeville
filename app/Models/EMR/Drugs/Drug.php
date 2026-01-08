<?php

namespace App\Models\EMR\Drugs;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Drug extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_drugs';
    protected $fillable = array('id', 'name', 'ham', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
	}
    
    public function details(){
    	return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}

    public function forms(){
    	return $this->hasMany('App\Models\EMR\DrugForm', 'drug_id', 'id');
	}

    public function hospital(){
    	return $this->belongsTo('App\Models\EMR\Hospital', 'hospital_id', 'id');
	}

    public function specific_drugs(){
        return $this->hasMany('App\Models\Inventory\Item', 'specific_id', 'id')->where('type_id', '=', 2);   
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
	}
    
}
