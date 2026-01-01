<?php

namespace App\Models\Sales;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Structure
{
    use HasFactory;

    public const PaymentStatusUnpaid = 0;
    public const PaymentStatusPaid = 10;
    public const PaymentStatusPartPayment = 5;
    public const PaymentStatus3rdPartyPending = 3;

    public const StatusDraft = 0;
    public const StatusPending = 1;
    public const StatusApproved = 2;
    public const StatusOngoing = 3;
    public const StatusDelivered = 10;
    public const StatusCancelled = 40;

    public const TypePostPaid = 1;
    public const TypePrePaid = 2;
    protected $primaryKey = 'id';
    protected $table = 'sales_orders';
    protected $fillable = array('unique_id', 'store_id', 'customer_id', 'customer_lpo', 'type_id', 'payment_term_id', 'payment_due_date', 'delivery_date', 'date', 'additional_cost', 'discount', 'taxes', 'logistics', 'description', 'status', 'payment_status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function customer(){
    	return $this->belongsTo('App\Models\CRM\Customer', 'customer_id', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function fulfillments(){
    	return $this->hasMany('App\Models\Sales\Fulfillment', 'customer_id', 'uuid');
    }

    public function grandAmount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $itemsTotal = $this->order_items->sum(function ($item) {
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

    public function grandTotal()
    {
        // use your existing accessor logic but return a float
        $itemsTotal = $this->order_items->sum(function ($item) {
            return (($item->unit_price * $item->quantity) - ($item->discount ?? 0));
        });

        $taxes = 0.075 * $itemsTotal; // replace if taxes are stored on the order
        $logistics = floatval($this->logistics ?? 0);
        $additional = floatval($this->additional_cost ?? 0);
        $orderDiscount = floatval($this->discount ?? 0);

        return round($itemsTotal + $taxes + $logistics + $additional - $orderDiscount, 2);
    }

    public function order_items(){
    	return $this->hasMany('App\Models\Sales\OrderItem', 'so_id', 'unique_id');
    }

    public function payment_term(){
    	return $this->belongsTo('App\Models\Procurement\PaymentTerm', 'payment_term_id', 'id');
    }

    public function payments(){
    	return $this->hasMany('App\Models\Finance\Payment', 'so_id', 'id');
    }

    public function returns(){
    	return $this->hasMany('App\Models\Sales\Return', 'customer_id', 'uuid');
    }

    public function store(){
    	return $this->belongsTo('App\Models\Inventory\Store', 'store_id', 'id');
    }

    public function totalAmount()
    {
        return Attribute::get(function () {
            $itemTotal = $this->order_items->sum(function ($item) {
                return ((floatval($item->unit_price) * intVal($item->quantity)) - floatval($item->discount) ?? 0.00);
            });

            return $itemTotal 
                + floatval(0.075 * $itemTotal) // 7.5% taxes
                + floatval($this->logistics)
                + floatval($this->additional_cost)
                - floatval($this->discount);
        });
    }

    public function getTotalCostAttribute(){
    // Step 1: Sum up all items' (unit_price * quantity) - discount
    $itemsTotal = $this->order_items->sum(function ($item) {
        return ($item->unit_price * $item->quantity) - $item->discount;
    });

    // Step 2: Add logistics & additional_cost, subtract order discount
    $subtotal = $itemsTotal + ($itemsTotal * 0.075) + $this->logistics + $this->additional_cost - $this->discount;

    return $subtotal;
    }

    public function getTotalFulfilledAttribute(){
    // Step 1: Sum up all items' (unit_price * quantity) - discount
    $itemsTotal = $this->order_items->sum(function ($item) {
        return ($item->unit_price * $item->fulfilled_quantity) - $item->discount;
    });

    // Step 2: Add logistics & additional_cost, subtract order discount
    $subtotal = $itemsTotal + ($itemsTotal * 0.075) + $this->logistics + $this->additional_cost - $this->discount;

    return $subtotal;
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
