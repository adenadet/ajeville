<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    const StatusPending = 0;
    const StatusConfirmed = 1;
    const StatusDeleted = 1000;
    protected $primaryKey = 'id';
    protected $table = 'procurement_batches';
    protected $fillable = array('unique_id', 'item_id', 'po_id', 'po_item_id', 'quantity', 'package_id', 'package_quantity', 'total_quantity', 'batch_number', 'expiry_date', 'status', 'confirmed_by', 'confirmed_at', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function approver(){
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
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

    public function purchase_order(){
    	return $this->belongsTo('App\Models\Procurement\PurchaseOrder', 'po_id', 'id');
    }

    public function purchase_order_item(){
    	return $this->belongsTo('App\Models\Procurement\PurchaseOrderItem', 'po_item_id', 'id');
    }
    
    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
    
}