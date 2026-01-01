<?php

namespace App\Models\Insurance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanBranch extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'hmo_plan_branches';
    protected $fillable = array('plan_id', 'branch_id', 'price_list_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function branch(){
        return $this->belongsTo('App\Models\Operations\Branch', 'branch_id', 'id');
    }
    
    public function plan(){
        return $this->belongsTo('App\Models\Insurance\Plan', 'provider_id', 'id');
    }
    
    public function price_list(){
        return $this->belongsTo('App\Models\Finance\PriceList', 'price_list_id', 'id');
    }
}
