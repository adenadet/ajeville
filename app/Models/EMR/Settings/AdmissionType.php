<?php

namespace App\Models\EMR\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionType extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_admission_types';
    protected $fillable = array('name', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
}
