<?php

namespace App\Models\EMR\Drugs;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_drug_forms';
    protected $fillable = array('id', 'name', 'description');

}
