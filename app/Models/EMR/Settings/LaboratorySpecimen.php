<?php

namespace App\Models\EMR\Settings;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratorySpecimen extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_settings_laboratory_specimens';
    protected $fillable = array('name', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
