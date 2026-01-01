<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnItemBatch extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'sales_return_item_batches';

    protected $fillable = ['id', 'return_id', 'item_id', 'item_name', 'unit_price', 'quantity', 'discount', 'reason', 'status', 'created_at', 'updated_at', 'deleted_at'];

    public function return_batches()
    {
        return $this->belongsTo('App\Models\Sales\OrderReturnItemBatch', 'return_item_id', 'id');
    }
}
