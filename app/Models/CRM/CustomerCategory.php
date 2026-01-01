<?php

namespace App\Models\CRM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'crm_customer_categories';
    protected $fillable = array('name', 'description', 'status', 'created_at', 'updated_at', 'deleted_at');

}
