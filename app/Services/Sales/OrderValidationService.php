<?php 
namespace App\Services\Sales;

use App\Models\Sales\Order;
use App\Models\Sales\OrderItem;
use Exception;

class OrderValidationService
{
    public function ensureOrderIsEditable(Order $order)
    {
        if (in_array($order->status, ['fulfilled', 'cancelled'])) { throw new Exception("Order is not editable.");}
    }

    public function ensureOrderIsFulfillable(Order $order)
    {
        if ($order->status !== 'approved') {throw new Exception("Order must be approved before fulfillment.");}
    }

    public function ensureItemIsFulfillable(OrderItem $item)
    {
        if ($item->status === 'fulfilled') { throw new Exception("Order item already fulfilled.");}

        if ($item->quantity_fulfilled >= $item->quantity) {throw new Exception("No remaining quantity to fulfill.");}
    }

    public function ensureOrderCanBeCancelled(Order $order)
    {
        if ($order->status === 'fulfilled') {throw new Exception("Fulfilled orders cannot be cancelled.");}
    }
}
