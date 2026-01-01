<?php

namespace App\Models\Equipments;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'equipment_assets';
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 4;
    const STATUS_DELETED = 6;

    protected $fillable = [
        'name', 'purchase_value', 'uuid', 'acquisition_date', 'depreciation_rate', 'type_id', 'serial_number', 'description', 'status', 'assigned_to_user_id', 'location_id', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function assignedUser()
    {
        return $this->belongsTo('App\Models\User', 'assigned_to_user_id', 'id'); 
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Equipments\AssetType', 'type_id', 'id'); 
    }

    public function creater()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
    }

    public function deleter()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Equipments\Location', 'location_id', 'id'); 
    }

    public function updater()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
    }

}