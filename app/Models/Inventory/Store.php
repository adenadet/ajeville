<?php

namespace App\Models\Inventory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'inventory_stores';
    protected $fillable = array('id', 'name', 'description', 'branch_id', 'department_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function branch(){
    	return $this->belongsTo('App\Models\Branch', 'branch_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function department(){
    	return $this->belongsTo('App\Models\Department', 'department_id', 'id');
    }

    public function items()
    {
        return $this->hasMany('App\Models\Inventory\StoreItem', 'store_id', 'id');
    }

    public function store_users()
    {
        return $this->hasMany('App\Models\Inventory\StoreUser', 'store_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
