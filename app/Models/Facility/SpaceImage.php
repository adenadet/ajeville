<?php

namespace App\Models\Facility;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceImage extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'facility_space_images';
}
