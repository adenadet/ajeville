<?php

namespace App\Models\Procurement;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'procurement_purchase_order_items';
    protected $fillable = array('po_id', 'item_id', 'item_name', 'quantity', 'approved_quantity', 'package_id', 'package_quantity', 'total_quantity', 'unit_price', 'discount', 'total_price', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function batches(){
    	return $this->hasMany('App\Models\Procurement\Batch', 'po_item_id', 'id');
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

    public function package(){
        return $this->belongsTo('App\Models\Procurement\PackageType', 'package_id', 'id');
    }

    public function purchase_order(){
        return $this->belongsTo('App\Models\Procurement\PurchaseOrder', 'po_id', 'unique_id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
