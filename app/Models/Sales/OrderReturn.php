<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class OrderReturn extends Structure
{
    use HasFactory;
    
    public const STATUS_CREATED = 1;
    public const STATUS_CONFIRMED = 10;
    public const STATUS_REJECTED = 100;
    protected $primaryKey = 'id';
    protected $table = 'sales_returns';

    protected $fillable = ['unique_id', 'sales_order_id', 'store_id', 'date', 'amount', 'customer_id', 'price_list_id', 'status', 'confirmed_by', 'confirmed_at', 'confirmed_remark', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = ['confirmed_at' => 'datetime',];

    public function confirmer()
    {
        return $this->belongsTo('App\Models\User', 'confirmed_by', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function customer(){
    	return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function grandAmountAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                $itemsTotal = $this->returned_items->sum(function ($item) {
                    return (($item->unit_price * $item->quantity) - ($item->discount ?? 0));
                });

                $taxes = 0.075 * $itemsTotal; 
                $logistics = floatval($this->logistics ?? 0);
                $additional = floatval($this->additional_cost ?? 0);
                $orderDiscount = floatval($this->discount ?? 0);

                return round($itemsTotal + $taxes + $logistics + $additional - $orderDiscount, 2);
            }
        );
    }
    
    public function returnItems()
    {
        return $this->hasMany('App\Models\Sales\OrderReturnItem', 'return_id', 'id');
    }

    public function sales_order()
    {
        return $this->belongsTo('App\Models\Sales\Order', 'sales_order_id', 'unique_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Models\Inventory\Store', 'store_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
