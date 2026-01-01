<?php

namespace App\Models\Inventory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class StoreUser extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'inventory_store_users';
    protected $fillable = array('store_id', 'user_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function store(){
        return $this->belongsTo('App\Models\Inventory\Store', 'store_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function user(){
    	return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
