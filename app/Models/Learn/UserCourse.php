<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class UserCourse extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'learn_user_courses';
    protected $fillable = array('user_id', 'course_id', 'level', 'assigned_date', 'start_date', 'expiry_date', 'user_start_time', 'user_finish_time', 'status', 'created_by', 'updated_by', 'deleted_by', 'deleted_at');
    
    public function course(){
    	return $this->belongsTo('App\Models\Learn\Course', 'course_id', 'id'); 
    }

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
    }

    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
    }
    
    public function user(){
    	return $this->belongsTo('App\Models\User', 'user_id', 'id'); 
	}
}