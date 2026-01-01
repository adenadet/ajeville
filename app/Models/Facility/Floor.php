<?php

namespace App\Models\Facility;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Structure
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'facility_floors';
    protected $fillable = ['building_id', 'name', 'level','total_area', 'layout_image', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function building(){
        return $this->belongsTo('App/Models/Facility/Building', 'building_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App/Models/User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App/Models/User', 'deleted_by', 'id');
    }
 
    public function spaces(){
        return $this->hasMany('App/Models/Facility/Space');
    }

    public function updater(){
        return $this->belongsTo('App/Models/User', 'updated_by', 'id');
    }
}
