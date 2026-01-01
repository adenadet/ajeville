<?php

namespace App\Models\Equipments;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    use HasFactory;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETED = 6;
    
    protected $primaryKey = 'id';
    protected $table = 'equipment_asset_types';
    
    protected $fillable = [
        'name', 'uuid', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

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
