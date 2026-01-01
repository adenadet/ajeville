<?php

namespace App\Models\Escrows;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'escrow_product_images';
    protected $fillable = array('id', 'product_id', 'source', 'primary', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    protected $hidden = [
        'created_by', 'updated_by',
    ];
}
