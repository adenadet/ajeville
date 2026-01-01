<?php

namespace App\Models\EMR\Settings;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequency extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_frequencies';
    protected $fillable = array('name', 'description', 'created_at', 'updated_at', 'deleted_at');
}
