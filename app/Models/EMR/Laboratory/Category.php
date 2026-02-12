<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Structure
{
    use HasFactory;

    protected $table = 'emr_settings_laboratory_categories';

    protected $fillable = ['name', 'description', 'status'];
}
