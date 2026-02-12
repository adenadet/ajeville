<?php

namespace App\Models\EMR\Radiology;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestigationType extends Model
{
    use HasFactory;

     protected $primaryKey = 'id';
    protected $table = 'emr_settings_radiology_investigation_types';
    protected $fillable = array('name', 'status', 'created_at', 'updated_at', 'deleted_at');

}
