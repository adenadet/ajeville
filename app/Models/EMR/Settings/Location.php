<?php

namespace App\Models\EMR\Settings;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_locations';
    protected $fillable = array('name', 'description', 'created_at', 'updated_at', 'deleted_at');

}