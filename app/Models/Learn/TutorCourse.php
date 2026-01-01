<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class TutorCourse extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'learn_tutor_courses';
    protected $fillable = array('tutor_id', 'course_id', 'start_date', 'end_date', 'created_by', 'updated_by', 'deleted_by', 'deleted_at');
    
    public function course(){
    	return $this->belongsTo('App\Models\Lms\Course', 'course_id', 'id'); 
		}
    
    public function tutor(){
    	return $this->belongsTo('App\Models\User', 'tutor_id', 'id'); 
    }
    
}