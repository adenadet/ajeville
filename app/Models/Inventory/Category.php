<?php

namespace App\Models\Inventory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Structure
{
    use HasFactory;
    
    protected $primaryKey = 'id';
    protected $table = 'inventory_item_categories';
    protected $fillable = array( 'name', 'description', 'primary_category_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function category(){
        return $this->belongsTo('App\Models\Inventory\Category', 'primary_category_id', 'id');
    }

    public function classification(){
        return $this->belongsTo('App\Models\Inventory\Classification', 'classification_id', 'id');
    }

    public function creater(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function item_type(){
        return $this->belongsTo('App\Models\Inventory\ItemType', 'item_type_id', 'id');
    }

    public function items(){
        return $this->hasMany('App\Models\Inventory\Item', 'category_id', 'id');
    }

    public function sub_categories(){
        return $this->hasMany('App\Models\Inventory\Category', 'primary_category_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
    
