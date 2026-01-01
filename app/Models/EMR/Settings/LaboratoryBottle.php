<?php

namespace App\Models\EMR\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryBottle extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_laboratory_bottles';
    protected $fillable = array('name', 'colour', 'size', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
}
