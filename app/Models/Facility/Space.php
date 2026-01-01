<?php

namespace App\Models\Facility;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Space extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInactive = 0;

    protected $primaryKey = 'id';
    protected $table = 'facility_spaces';
    protected $fillable = ['name', 'code', 'type_id', 'department_id', 'floor_id', 'building_id', 'area', 'capacity', 'status', 'description', 'is_available', 'created_by', 'updated_by', 'deleted_by','created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function type()
    {
        return $this->belongsTo('/App/Models/Facility/SpaceType');
    }

    public function images(){
        return $this->hasMany('/App/Models/Facility/SpaceImage');
    }

    public function department()
    {
        return $this->belongsTo('/App/Models/Operations/Department');
    }

    public function floor()
    {
        return $this->belongsTo('/App/Models/Facility/Floor');
    }

    public function building()
    {
        return $this->belongsTo('/App/Models/Facility/Building');
    }

    public function bookings()
    {
        return $this->hasMany('/App/Models/Facility/SpaceBooking');
    }

    public function assets()
    {
        return $this->hasMany('/App/Models/Facility/Asset');
    }

    public function maintenanceRequests()
    {
        return $this->hasMany('/App/Models/Facility/MaintenanceRequest');
    }
}
