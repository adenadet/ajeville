<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bottle extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_laboratory_bottles';
    protected $fillable = array('name', 'description', 'status', 'colour', 'size', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
}