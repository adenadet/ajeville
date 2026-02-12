<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInActive = 0;

    protected $primaryKey = 'id';
    protected $table = 'emr_admission_rooms';
    protected $fillable = array('name', 'ward_id', 'room_type_id', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id');
    }

    public function room_type(){
    	return $this->belongsTo('App\Models\EMR\Admission\RoomType', 'room_type_id', 'id');
    }

    public function ward(){
    	return $this->belongsTo('App\Models\EMR\Admission\Ward', 'ward_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }
}
