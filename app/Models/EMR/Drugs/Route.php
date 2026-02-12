<?php

namespace App\Models\EMR\Drugs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'emr_drug_routes';
    protected $fillable = array('id', 'name', 'description');

}
