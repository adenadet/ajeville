<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Structure
{
    use HasFactory;

    const StatusActive = 1;
    const StatusInActive = 0;
    protected $primaryKey = 'id';
    protected $table = 'emr_admission_room_types';
    protected $fillable = array('name', 'item_id', 'description', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function admission_service(){
    	return $this->belongsTo('App\Models\EMR\Admission\Service', 'id', 'room_type_id');
    }

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
