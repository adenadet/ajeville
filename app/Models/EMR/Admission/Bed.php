<?php

namespace App\Models\EMR\Admission;

use App\Models\Structure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Structure
{
    use HasFactory;

    const AssignmentStatusFree = 0;
    const AssignmentStatusInUse = 1;
    const StatusActive = 1;
    const StatusInActive = 0;

    protected $primaryKey = 'id';
    protected $table = 'emr_admission_beds';
    protected $fillable = array('name', 'ward_id', 'room_id', 'room_type_id', 'bed_code', 'assignment_status', 'status', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at');

    public function room(){
        return $this->belongsTo('App\Models\EMR\Admission\Room', 'room_id', 'id');
    }

    public function room_type(){
        return $this->belongsTo('App\Models\EMR\Admission\RoomType', 'room_type_id', 'id');
    }

    public function ward(){
        return $this->belongsTo('App\Models\EMR\Admission\Ward', 'ward_id', 'id');
    }
}
