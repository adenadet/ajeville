<?php

namespace App\Models\Insurance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'hmo_provider_plans';
    protected $fillable = array('name', 'provider_id', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function branches(){
        return $this->hasManyThrough('App\Models\Operations\Branch', 'App\Models\Insurance\PlanBranch', 'plan_id', 'id', 'id', 'branch_id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function patients(){
        return $this->hasManyThrough('App\Models\EMR\Patient', 'App\Models\EMR\PatientInsurance', 'plan_id', 'id', 'id', 'patient_id');
    }

    public function price_lists(){
    	return $this->hasMany('App\Models\Finance\PriceList', 'id', 'plan_id');
	}

    public function provider(){
    	return $this->belongsTo('App\Models\Insurance\Provider', 'provider_id', 'id');
	}

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
