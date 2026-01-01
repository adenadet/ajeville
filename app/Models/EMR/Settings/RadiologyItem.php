<?php

namespace App\Models\EMR\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RadiologyItem extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_radiology_items';
    protected $fillable = array('name', 'result_template_id', 'bottle_id');
}
