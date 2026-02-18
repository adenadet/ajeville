<?php

namespace App\Models\Inventory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Structure
{
    use HasFactory;

    const StatusActive = "active";
    const StatusInactive = "inactive";
    
    protected $primaryKey = 'id';
    protected $table = 'inventory_items';
    protected $fillable = array('name', 'type_id', 'classification_id', 'category_id', 'unique_id', 'service_id', 'specific_id', 'brand_id', 'image', 'barcode', 'last_landing_cost', 'average_landing_cost', 'description', 'status', 'created_by', 'service_type_id', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function brand(){    
        return $this->belongsTo('App\Models\Inventory\Brand', 'brand_id', 'id');
    }

    public function category(){    
        return $this->belongsTo('App\Models\Inventory\Category', 'category_id', 'id');
    }

    public function classification(){
        return $this->belongsTo('App\Models\Inventory\Classification', 'classification_id', 'id');
    }

    public function emr_service(){
        return $this->belongsTo('App\Models\EMR\Service', 'service_id', 'id');
    }

    public function item_type(){
        return $this->belongsTo('App\Models\Inventory\ItemType', 'type_id', 'id');
    }

    public function returned_items(){
    	return $this->hasMany('App\Models\Inventory\ReturnItem', 'id', 'item_id');
    }

    public function sales_order(){
    	return $this->hasMany('App\Models\Sales\OrderItem', 'item_id', 'id');
    }

    public function service(){
        return $this->hasOne('App\Models\EMR\Service', 'item_id', 'id');
    }

    public function service_type(){
        return $this->belongsTo('App\Models\EMR\Settings\ServiceType', 'service_type_id', 'id');
    }
}
