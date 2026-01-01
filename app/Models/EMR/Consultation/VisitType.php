<?php

namespace App\Models\EMR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class VisitType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_visit_types';
    protected $fillable = array('name', 'description', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_by', 'deleted_at');
}
