<?php

namespace App\Models\Operations;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'operation_modules';
    protected $fillable = array('name', 'icon', 'link', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
    
    public function roles()
    {
        return $this->belongsToManyThrough('App/Models/Ums/Role', 'App/Models/Ums/ModuleRole', 'module_id', 'role_id');
    }

    public function users()
    {
        return $this->belongsToManyThrough('App/Models/User', 'App/Models/Ums/ModuleUser', 'module_id', 'user_id');
    }
}
