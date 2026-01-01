<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class UserCourse extends Structure
{
    const StatusAssigned = 0;
    const StatusOngoing = 1;
    const StatusCompleted = 10;
    const StatusQueried = 100;

    protected $primaryKey = 'id';
    protected $table = 'learn_user_courses';
    protected $fillable = array('user_id', 'course_id', 'level', 'assigned_date', 'start_date', 'expiry_date', 'user_start_time', 'user_finish_time', 'status');
    
    public function course(){
    	return $this->belongsTo('App\Models\Lms\Course', 'course_id', 'id'); 
        }
    
    public function user(){
    	return $this->belongsTo('App\Models\User', 'user_id', 'id'); 
		}
}