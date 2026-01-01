<?php

namespace App\Models\Operations;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;
class Branch extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'operation_branches';
    protected $fillable = array('name', 'address', 'short', 'unique_id', 'phone', 'price_list_id', 'cinc_id', 'pm_id', 'hon_id', 'current', 'status');
	
	public function chief_consultant(){
		return $this->belongsTo('App\Models\HRMS\Employee', 'cinc_id', 'id');
	}

	public function head_nurse(){
		return $this->belongsTo('App\Models\HRMS\Employee', 'hon_id', 'id');
	}

	public function practice_manager(){
		return $this->belongsTo('App\Models\HRMS\Employee', 'pm_id', 'id');
	}

	public function users(){
    	return $this->hasMany('App\Models\User', 'branch_id', 'id');
	}

	public function modules(){
    	return $this->hasManyThrough('App\Models\Operations\Module', 'App\Models\Operations\BranchModule', 'branch_id', 'id', 'id', 'module_id');
	}
	
	public function price_list(){
    	return $this->belongsTo('App\Models\Finance\PriceList', 'price_list_id', 'id');
	}
}