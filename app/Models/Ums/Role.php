<?php

namespace App\Models\Ums;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'roles';
    protected $fillable = array('name', 'guard_name',);

}
