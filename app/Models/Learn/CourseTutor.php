<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Model;

class CourseTutor extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'learn_course_tutors';
    protected $fillable = array('tutor_id', 'course_id', 'created_by', 'start_date', 'end_date',);
    
    public function creator(){
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    public function course(){
    	return $this->belongsTo('App\Models\Learn\Course', 'course_id', 'id'); 
	}
    
    public function tutor(){
    	return $this->belongsTo('App\Models\User', 'tutor_id', 'id'); 
    }

}
