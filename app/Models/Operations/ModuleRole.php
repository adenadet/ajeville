<?php

namespace App\Models\Operations;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleRole extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'operation_module_roles';
    protected $fillable = array('role_id', 'module_id', 'created_at', 'updated_at', 'deleted_at');
    
}
