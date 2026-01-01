<?php

namespace App\Models\Operations;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleUser extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'operation_module_users';
    protected $fillable = array('user_id', 'module_id', 'created_at', 'updated_at', 'deleted_at');
    
}