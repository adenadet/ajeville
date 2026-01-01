<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'inventory_package_items';
    protected $fillable = array('item_id', 'quantity', 'created_at', 'updated_at', 'deleted_at');

    public function items(){
    	return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }
}
