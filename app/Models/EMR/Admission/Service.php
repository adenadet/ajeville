<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_admission_services';
    protected $fillable = array('item_id', 'category_id', 'sub_category_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
	
    public function bottle_type(){
		return $this->belongsTo('App\Models\Operations\BottleType', 'bottle_type_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function item(){
    	return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

	public function result_template(){
		return $this->belongsTo('App\Models\Operations\ResultTemplate', 'result_template_id', 'id');
    }

    public function specimen(){
		return $this->belongsTo('App\Models\Operations\Specimen', 'specimen_id', 'id');
    }
    
    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}