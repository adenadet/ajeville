<?php

namespace App\Models\EMR\Settings;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_service_types';
    protected $fillable = array('name', 'status', 'description', 'queueable', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function services(){
        return $this->hasMany('App\Models\EMR\Service', 'service_type_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
