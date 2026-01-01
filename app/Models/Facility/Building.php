<?php

namespace App\Models\Facility;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'facility_buildings';
    protected $fillable = ['name', 'code', 'address', 'location', 'year_built', 'total_floors', 'total_area', 'owner', 'status', 'description', 'created_by', 'updated_by', 'deleted_by','created_at', 'updated_at', 'deleted_at'];


    public function images()
    {
        return $this->hasMany(BuildingImage::class);
    }

    public function floors()
    {
        return $this->hasMany(Floor::class);
    }

    public function spaces()
    {
        return $this->hasMany(Space::class);
    }
}
