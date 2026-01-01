<?php

namespace App\Models\Sales;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnItem extends Structure
{
    use HasFactory;

    public const STATUS_CREATED = 1;
    public const STATUS_CONFIRMED = 10;
    public const STATUS_PENDING = 4;
    public const STATUS_REJECTED = 100;
    protected $primaryKey = 'id';
    protected $table = 'sales_return_items';

    protected $fillable = ['id', 'return_id', 'item_id', 'item_name', 'unit_price', 'quantity', 'discount', 'reason', 'status', 'created_at', 'updated_at', 'deleted_at'];

    public function return_batches()
    {
        return $this->hasMany('App\Models\Sales\OrderReturnItemBatch', 'return_item_id', 'id');
    }
}
