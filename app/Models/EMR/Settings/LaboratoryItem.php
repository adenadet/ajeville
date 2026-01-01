<?php

namespace App\Models\EMR\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryItem extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_laboratory_items';
    protected $fillable = array('name', 'result_template_id', 'bottle_id');
}
