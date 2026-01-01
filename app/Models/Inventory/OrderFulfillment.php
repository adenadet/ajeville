<?php

namespace App\Models\Inventory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFulfillment extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'inventory_order_fulfillments';

    protected $fillable = array('uuid', 'reference_id', 'store_item_id', 'batch_id', 'quantity', 'type', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    
    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function delivery_person(){
    	return $this->belongsTo('App\Models\User', 'delivery_by', 'id');
    }

    public function order(){
    	return $this->belongsTo('App\Models\Sales\Order', 'so_id', 'uuid');
    }

    public function order_items(){
    	return $this->hasMany('App\Models\Sales\OrderItem', 'item_id', 'unique_id');
    }

    public function store_item_batch(){
    	return $this->belongsTo('App\Models\Inventory\StoreItemBatch', 'batch_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

}
