<?php

namespace App\Models\EMR\Laboratory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;

    protected $table = 'emr_laboratory_request_items';

    protected $fillable = [
        'request_id', 'service_id', 'panel_id', 'category_id', 'status', 'tat_minutes', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');    
    }

    public function deleter(){
        return $this->belongsTo('App\Models\User', 'deleted_by', 'id');    
    }

    public function request(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Request', 'request_id', 'id');
    }

    public function service(){
        return $this->belongsTo('App\Models\EMR\Laboratory\Service', 'service_id', 'id');
    }

    public function specimen(){
        return $this->hasOne('App\Models\EMR\Laboratory\Specimen', 'request_item_id', 'id');
    }

    public function result(){
        return $this->hasOne('App\Models\EMR\Laboratory\Result', 'request_item_id', 'id');
    }

    public function queueEntries(){
        return $this->hasMany('App\Models\EMR\Laboratory\Queue', 'request_item_id', 'id');
    }

    public function updater(){
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');    
    }
}
