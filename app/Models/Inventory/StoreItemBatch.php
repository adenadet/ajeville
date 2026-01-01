<?php

namespace App\Models\Inventory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreItemBatch extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'inventory_store_item_batches';
    protected $fillable = array('store_item_id', 'batch_id', 'received', 'balance', 'transferred', 'issued', 'sold', 'status', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');

    public function batch(){
    	return $this->belongsTo('App\Models\Procurement\Batch', 'batch_id', 'id');
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

    public function store_item(){
        return $this->belongsTo('App\Models\Inventory\StoreItem', 'store_item_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
