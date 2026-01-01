<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'sales_quotation_items';
    protected $fillable = [ 'uuid', 'quotation_id', 'item_id', 'description', 'quantity', 'package_id', 'package_quantity', 'unit_price', 'total_price', 'created_at', 'updated_at', 'deleted_at'];

    public function item()
    {
        return $this->belongsTo('App\Models\Inventory\Item', 'item_id', 'id');
    }

    public function invoice()
    {
        return $this->hasOne('App\Models\Sales\Order', 'quotation_id', 'uuid');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function package(){
        return $this->belongsTo('App\Models\Procurement\PackageType', 'package_id', 'id');
    }               

    public function quotation()
    {
        return $this->belongsTo('App\Models\Sales\Quotation', 'quotation_id', 'uuid');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
