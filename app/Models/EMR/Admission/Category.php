<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInActive = 0;

    protected $primaryKey = 'id';
    protected $table = 'emr_admission_categories';
    protected $fillable = array('name', 'description', 'status', 'created_at', 'updated_at', 'deleted_at');

}

