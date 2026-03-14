<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_services';
    protected $fillable = array('service_id', 'category_id', 'bottle_type_id', 'result_template_id', 'specimen_type_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function analytes(){
        return $this->hasManyThrough('App\Models\EMR\Laboratory\Analyte', 'App\Models\EMR\Laboratory\ServiceAnalyte', 'service_id','id', 'id', 'analyte_id');
    }

    public function bottle_type(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Bottle', 'bottle_type_id', 'id');
    }
    
    public function category(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Category', 'category_id', 'id');
    }

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function emr_service(){
        return $this->belongsTo('App\Models\EMR\Service', 'service_id', 'id');
    }

    public function service_analytes(){
        return $this->hasMany('App\Models\EMR\Laboratory\ServiceAnalyte', 'service_id', 'id');
    }
    
    public function specimen_type(){
        return $this->belongsTo('App\Models\EMR\Laboratory\SpecimenType', 'specimen_type_id', 'id');
    }
    
    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}