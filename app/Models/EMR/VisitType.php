<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Structure;

class VisitType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_visit_types';
    protected $fillable = array('name', 'description', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');
}