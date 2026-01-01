<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceListItem extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'finance_price_list_items';
    protected $fillable = array('item_id', 'price_list_id', 'price', 'covered', 'coverage', 'requires_code', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function item(){
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function price_list(){
        return $this->belongsTo('App\Models\Finance\PriceList', 'price_list_id', 'id');
    }
}
