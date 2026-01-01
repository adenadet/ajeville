<?php

namespace App\Models\EMR\Settings;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duration extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_durations';
    protected $fillable = array('name','created_at', 'updated_at', 'deleted_at');
}
