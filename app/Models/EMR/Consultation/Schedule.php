<?php

namespace App\Models\EMR\Consultation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table = 'emr_consultant_schedules';
    protected $fillable = array('consultant_id', 'branch_id', 'day_of_week', 'start_time', 'end_time', 'slot_duration', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at');

    public function branch(){
    	return $this->belongsTo('App\Models\Operation\Branch', 'branch_id', 'id');
	}

    public function consultant(){
    	return $this->belongsTo('App\Models\User', 'consultant_id', 'id');
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
