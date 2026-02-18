<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecimenType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_laboratory_specimen_types';
    protected $fillable = array('name', 'storage_temp', 'stability_duration', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');    
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');    
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');    
    }
}