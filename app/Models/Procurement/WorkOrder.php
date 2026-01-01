<?php

namespace App\Models\Procurement;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'procurement_work_orders';
    protected $fillable = array('unique_id', 'branch_id', 'department_id', 'vendor_id', 'type_id', 'payment_term_id', 'delivery_date', 'date', 'additional_cost', 'taxes', 'logistics', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function approvals(){
        return $this->hasMany('App\Models\Approvals\Details', 'wo_id', 'item_id');
    }

    public function branch(){
    	return $this->belongsTo('App\Models\Inventory\Store', 'department_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function department(){
    	return $this->belongsTo('App\Models\Inventory\Store', 'department_id', 'id');
    }

    public function order_items(){
        return $this->hasMany('App\Models\Procurement\WorkOrderItem', 'wo_id', 'id');
    }

    public function payment_term(){
    	return $this->belongsTo('App\Models\Procurement\PaymentTerm', 'payment_term_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function vendor(){
    	return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }
}
