<?php

namespace App\Models\Ums;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Structure;

class CompanyShareholder extends Structure

{
    use HasFactory;

    protected $table = 'user_company_shareholders';
    protected $fillable = array('uuid', 'name', 'bvn', 'id_card_type', 'id_card', 'status', 'status_description', 'status_by', 'status_at', 'created_at', 'updated_at', 'deleted_at'); 

    protected $hidden = array('created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    protected $casts = [
        'status' => 'boolean',
    ];
}