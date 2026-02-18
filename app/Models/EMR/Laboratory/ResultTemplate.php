<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultTemplate extends Model
{
    use HasFactory;

    protected $table = 'emr_settings_laboratory_result_templates';

    protected $fillable = ['name', 'layout', 'structured_json', 'status', 'description', 'created_by', 'updated_by', 'deleted_by',  'created_at', 'updated_at', 'deleted_at'];


    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');    
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');    
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');    
    }

    public function versions()
    {
        return $this->hasMany('App\Models\EMR\Laboratory\ResultTemplateVersion');
    }

    public function currentVersion()
    {
        return $this->hasOne('App\Models\EMR\Laboratory\ResultTemplateVersion')->where('is_current', true);
    }
}
