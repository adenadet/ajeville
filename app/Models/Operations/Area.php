<?php

namespace App\Models\Operations;
use App\Models\Structure;
use Illuminate\Database\Eloquent\Model;

class Area extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'operation_areas';
    protected $fillable = array('name', 'state_id');
}
