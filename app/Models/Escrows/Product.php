<?php

namespace App\Models\Escrows;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'escrow_products';
    protected $fillable = array('owner_id', 'item_code', 'category_id', 'image', 'role', 'description', 'details', 'detailed', 'unit_price', 'status', 'quantity', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    protected $hidden = [
        'created_by', 'updated_by',
    ];

    public function category(){
        return $this->belongsTo('App\Models\Escrows\ItemType', 'category_id', 'id');
    }

    public function images(){
        return $this->hasMany('App\Models\Escrows\ProductImage', 'product_id', 'id');
    }

    public function owner(){
        return $this->belongsTo('App\Models\User', 'owner_id', 'id');
    }
    
    public function transactions(){
		return $this->hasMany('App\Models\Escrows\Transaction', 'product_id', 'id');
	}
}
