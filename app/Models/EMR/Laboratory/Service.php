<?php

namespace App\Models\EMR\Laboratory;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'emr_laboratory_services';
    protected $fillable = array('service_id', 'category_id', 'bottle_type_id', 'specimen_type_id', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function bottle(){
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

    public function service(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Specimen', 'specimen_type_id', 'id');
    }

    public function specimen(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Specimen', 'specimen_type_id', 'id');
    }
    
    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

}