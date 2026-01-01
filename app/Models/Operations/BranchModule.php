<?php

namespace App\Models\Operations;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchModule extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'operation_branch_modules';
    protected $fillable = array('module_id', 'branch_id',);
    
}
