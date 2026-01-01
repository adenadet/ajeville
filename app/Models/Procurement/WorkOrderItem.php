<?php

namespace App\Models\Procurement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'procurement_work_order_items';
    protected $fillable = array('wo_id', 'item', 'quantity', 'unit_price', 'total_price', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function getTotalAmountAttribute()
    {
        // Sum item-level computed totals
        $items_total = $this->order_items->sum(function ($item) {
            $unit = $item->unit_price;
            $qty  = $item->total_quantity;
            $discount = ($item->discount ?? 0);

            return ($unit * $qty) - $discount;
        });

        // Add work order level costs
        $logistics       = ($this->logistics ?? 0);
        $taxes           = ($this->taxes ?? 0);
        $additional_cost = ($this->additional_cost ?? 0);
        $discount        = ($this->discount ?? 0); // only if column exists

        return $items_total + $logistics + $taxes + $additional_cost - $discount;
    }

    public function work_order(){
        return $this->belongsTo('App\Models\Procurement\WorkOrder', 'wo_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
