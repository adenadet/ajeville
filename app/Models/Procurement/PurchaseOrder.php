<?php

namespace App\Models\Procurement;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Structure
{
    use HasFactory;

    const StatusDraft = 0;
    const StatusAwaitingApproval = 1;
    const StatusApproved = 2;
    const StatusCompleted = 10;
    const StatusDeleted = 1000;
    const StatusRejected = 100;


    protected $primaryKey = 'id';
    protected $table = 'procurement_purchase_orders';
    protected $fillable = array('unique_id', 'store_id', 'vendor_id', 'type_id', 'payment_term_id', 'delivery_date', 'date', 'additional_cost', 'taxes', 'logistics', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function approvals(){
        return $this->hasMany('App\Models\Procurement\PurchaseOrderApproval', 'po_id', 'id');
    }

    public function batches(){
        return $this->hasMany('App\Models\Procurement\Batch', 'po_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function order_items(){
        return $this->hasMany('App\Models\Procurement\PurchaseOrderItem', 'po_id', 'unique_id');
    }

    public function payment_term(){
    	return $this->belongsTo('App\Models\Procurement\PaymentTerm', 'payment_term_id', 'id');
    }

    public function store(){
    	return $this->belongsTo('App\Models\Inventory\Store', 'store_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function vendor(){
    	return $this->belongsTo('App\Models\Procurement\Vendor', 'vendor_id', 'id');
    }


    // ... existing constants / properties / relations ...

    /**
     * Accessor: total_amount attribute.
     * Uses loaded order_items (no extra query if order_items already eager loaded).
     * Calculation:
     *   sum(item.unit_price * item.quantity - item.item_discount)
     * + order-level additional_cost + taxes + logistics - order-level discount
     *
     * Assumptions:
     * - Per-item discount field may be named 'discount' or 'item_discount'. If neither exists, treated as 0.
     * - Order-level discount field may be 'discount' or 'po_discount'. If absent, treated as 0.
     */
    
    public function getTotalAmountAttribute()
    {
        $items = $this->relationLoaded('order_items') ? $this->order_items : $this->order_items()->get();

        $itemsTotal = $items->reduce(function ($carry, $item) {
            // pick the quantity field (fallback to total_quantity or approved_quantity if needed)
            $quantity = $item->quantity ?? $item->total_quantity ?? $item->approved_quantity ?? 0;
            $unitPrice = $item->unit_price ?? 0;

            // per-item discount — try common names, else 0
            $itemDiscount = 0;
            if (isset($item->discount)) $itemDiscount = $item->discount;
            elseif (isset($item->item_discount)) $itemDiscount = $item->item_discount;
            // if total_price exists and is already precomputed, prefer explicit computation above

            $line = ($unitPrice * $quantity) - (float)$itemDiscount;
            return $carry + $line;
        }, 0.0);

        $additionalCost = (float) ($this->additional_cost ?? 0);
        $taxes = (float) ($this->taxes ?? 0);
        $logistics = (float) ($this->logistics ?? 0);

        // order-level discount: try several common field names
        $orderDiscount = 0;
        if (isset($this->discount)) $orderDiscount = $this->discount;
        elseif (isset($this->po_discount)) $orderDiscount = $this->po_discount;

        $total = $itemsTotal + $additionalCost + $taxes + $logistics - $orderDiscount;

        // ensure non-negative (optional)
        return $total < 0 ? 0 : $total;
    }

    /**
     * Efficient DB-side aggregate of total amount.
     * Returns a float.
     *
     * This constructs a single query that sums (unit_price * quantity - COALESCE(item_discount,0))
     * from procurement_purchase_order_items joined to this PO, then adds PO-level costs.
     *
     * IMPORTANT: adjusts the item-discount and order-discount column names if your schema uses different names.
     */
    public function totalAmountFromDb()
    {
        // item discount column name used in this SQL; change if your schema uses different field
        $itemDiscountCol = 'discount';        // try 'discount' or 'item_discount'
        $poDiscountCol = 'discount';          // try 'discount' or 'po_discount'

        $itemDiscountExpr = "COALESCE(procurement_purchase_order_items.{$itemDiscountCol}, 0)";

        // compute items subtotal
        $itemsSubtotal = DB::table('procurement_purchase_order_items')
            ->where('po_id', '=', $this->unique_id) // matches your relation: po_id => purchase_order.unique_id
            ->selectRaw("COALESCE(SUM( (COALESCE(unit_price,0) * COALESCE(quantity,0)) - {$itemDiscountExpr}), 0) as items_total")
            ->value('items_total');

        $additionalCost = (float) ($this->additional_cost ?? 0);
        $taxes = (float) ($this->taxes ?? 0);
        $logistics = (float) ($this->logistics ?? 0);
        $orderDiscount = 0;
        if (isset($this->discount)) $orderDiscount = $this->discount;
        elseif (isset($this->po_discount)) $orderDiscount = $this->po_discount;

        $total = (float) $itemsSubtotal + $additionalCost + $taxes + $logistics - (float) $orderDiscount;

        return $total < 0 ? 0 : $total;
    }

    /**
     * Optional helper: add a scope to select the computed total in a query (raw SQL).
     * Usage: PurchaseOrder::withTotal()->get();
     */
    public function scopeWithTotal($query)
    {
        // NOTE: this uses procurement_purchase_order_items.po_id = procurement_purchase_orders.unique_id
        $itemDiscountCol = 'discount';
        $poDiscountCol = 'discount';

        $itemsSumSql = "(
            SELECT COALESCE(SUM((COALESCE(ppoi.unit_price,0) * COALESCE(ppoi.quantity,0)) - COALESCE(ppoi.{$itemDiscountCol},0)), 0)
            FROM procurement_purchase_order_items ppoi
            WHERE ppoi.po_id = procurement_purchase_orders.unique_id
        )";

        $poFields = "COALESCE(procurement_purchase_orders.additional_cost,0) + COALESCE(procurement_purchase_orders.taxes,0) + COALESCE(procurement_purchase_orders.logistics,0) - COALESCE(procurement_purchase_orders.{$poDiscountCol},0)";

        return $query->selectRaw("procurement_purchase_orders.*, ({$itemsSumSql}) + ({$poFields}) as total_amount");
    }
}