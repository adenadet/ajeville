<?php

namespace App\Models\EMR\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_locations';
    protected $fillable = array('name','created_at', 'updated_at', 'deleted_at');

}
