<?php

namespace App\Models\Operations;
use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchPlanPriceList extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'operation_branch_plan_price_lists';
    protected $fillable = array('branch_id', 'plan_id', 'price_list_id', 'description',  'status','created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function branch(){
		return $this->belongsTo('App\Models\Operations\Branch', 'branch_id', 'id');
	}

	public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

	public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function plan(){
		return $this->belongsTo('App\Models\Insurance\Plan', 'plan_id', 'id');
	}

	public function price_list(){
		return $this->belongsTo('App\Models\Finance\PriceList', 'price_list_id', 'id');
	}

	public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
