<?php

namespace App\Models\Sales;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Structure
{
    use HasFactory;

    public const StatusPending = 0;
    public const StatusOngoing = 5;
    public const StatusFulfilled = 10;

    protected $primaryKey = 'id';
    protected $table = 'sales_order_items';
    protected $fillable = array('uuid', 'so_id', 'item_id', 'quantity', 'requested_quantity', 'approved_quantity', 'package_id', 'package_quantity', 'total_quantity', 'fulfilled_quantity', 'unit_price', 'total_price', 'discount', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function batches(){
    	return $this->hasMany('App\Models\Procurement\Batch', 'po_item_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function fulfillments()
    {
        return $this->hasMany('App\Models\Inventory\OrderFulfillment', 'reference_id', 'id')->where('type', 'sold');
    }

    public function item(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function order(){
        return $this->belongsTo('App\Models\Sales\Order', 'so_id', 'unique_id');
    }

    public function package(){
        return $this->belongsTo('App\Models\Procurement\PackageType', 'package_id', 'id');
    }

    public function sales_order(){
        return $this->belongsTo('App\Models\Sales\Order', 'so_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
