<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_admission_types';
    protected $fillable = array('name', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
}
