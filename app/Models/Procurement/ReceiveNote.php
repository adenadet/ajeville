<?php

namespace App\Models\Procurement;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveNote extends Structure
{
    use HasFactory;

    
    protected $primaryKey = 'id';
    protected $table = 'procurement_purchase_order_items';
    protected $fillable = array('id', 'po_id', 'item_id', 'batch_number', 'expiry_date', 'quantity', 'remark', 'created_by', 'updated_by', 'deleted_by', 'approved_by', 'approval_remark', 'approved_at', 'created_at', 'updated_at', 'deleted_at');

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

    public function package(){
        return $this->belongsTo('App\Models\Procurement\PackageType', 'package_id', 'id');
    }

    public function purchase_order(){
        return $this->belongsTo('App\Models\Procurement\PurchaseOrder', 'po_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
