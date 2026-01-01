<?php

namespace App\Models\Facility;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingImage extends Structure
{
    const StatusActive = 1;
    const StatusInactive = 0;
    
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'facility_building_images';
    protected $fillable = ['building_id', 'source', 'description', 'is_primary', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];
    
}
