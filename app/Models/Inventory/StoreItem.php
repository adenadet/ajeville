<?php

namespace App\Models\Inventory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreItem extends Structure
{
    
    protected $primaryKey = 'id';
    protected $table = 'inventory_store_item_settings';
    protected $fillable = array('store_id', 'item_id', 'reorder_level', 'maximum_level', 'expiry_notification', 'description', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function batches()
    {
        return $this->hasMany('App\Models\Inventory\StoreItemBatch', 'store_item_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function item(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }
    
    public function store(){
        return $this->belongsTo('App\Models\Inventory\Store', 'store_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
