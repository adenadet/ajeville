<?php

namespace App\Models\Finance;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'finance_price_lists';
    protected $fillable = array('unique_id', 'name', 'type_name', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    
    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    
    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function branch(){
        return $this->belongsTo('App\Models\Operations\Branch', 'branch_id', 'id');
    }

    public function plan(){
        return $this->belongsTo('App\Models\Insurance\Plan', 'plan_id', 'id');
    }

    public function price_list_items(){
        return $this->hasMany('App\Models\Finance\PriceListItem', 'price_list_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
