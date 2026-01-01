<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Course extends Structure {
    protected $primaryKey = 'id';
    protected $table = 'learn_courses';
    protected $fillable = array('name', 'category_id', 'sub_category_id', 'price', 'exam', 'exam_type_id', 'certificate_type_id', 'description');
        
    public function assignees(){
        return $this->hasMany('App\Models\Learn\UserCourse', 'course_id', 'id');
    }

    public function category(){
    	return $this->belongsTo('App\Models\Learn\Category', 'category_id', 'id'); 
	}

    public function creator(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
	}
    
    public function deleter(){
    	return $this->belongsTo('App\Models\User', 'deleted_by', 'id'); 
	}

    public function lessons(){
        return $this->hasMany('App\Models\Learn\Lesson', 'course_id', 'id');
    }

    public function sub_category(){
    	return $this->belongsTo('App\Models\Learn\SubCategory', 'sub_category_id', 'id'); 
	}

    public function subscribers()
    {
        return $this->hasManyThrough(
            'App\Models\User', 
            'App\Models\Lms\UserCourse', 
            'course_id', //Foreign key on UserCourse
            'id', //Foreign key on User table
            'id', // Local key on Course table
            'user_id', //Local key on User Course 
        );
    }

    public function tutors(){
        return $this->hasMany('App\Models\Learn\TutorCourse', 'course_id', 'id');
    }

    public function updater(){
    	return $this->belongsTo('App\Models\User', 'created_by', 'id'); 
	}
}
