<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Structure
{
    use HasFactory;
    const StatusActive = 1;
    const StatusInActive = 0;
    const TypeAccomodation = 1;
    protected $primaryKey = 'id';
    protected $table = 'emr_admission_services';
    protected $fillable = array('service_id', 'category_id', 'room_type_id', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');
	
    public function category(){
    	return $this->belongsTo('App\Models\EMR\Admission\Category', 'category_id', 'id');
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function emr_service(){
    	return $this->belongsTo('App\Models\EMR\Service', 'service_id', 'id');
    }

    public function room_type(){
    	return $this->belongsTo('App\Models\EMR\Admission\RoomType', 'room_type_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}