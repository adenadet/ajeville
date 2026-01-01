<?php

namespace App\Models\Facility;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpaceType extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'facility_space_types';

    protected $fillable = [
        'name', 'uuid', 'description', 'rate_per_hour', 'rate_per_day', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    protected $casts = [
        'rate_per_hour' => 'decimal:2',
        'rate_per_day' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function spaces()
    {
        return $this->hasMany('App/Models/Facility/Space');
    }
}
