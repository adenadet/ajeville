<?php

namespace App\Models\Learn;

use Illuminate\Database\Eloquent\Model;
use App\Models\Structure;

class Exam extends Structure
{
    protected $primaryKey = 'id';
    protected $table = 'learn_course_exams';
    protected $fillable = array('name', 'status', 'description', 'course_id', 'lesson_id', 'question', 'pass_mark', 'trials');

    public function assignees(){
        return $this->hasMany('App\Models\Learn\UserExam', 'exam_id', 'id');
    } 

    public function course(){
        return $this->belongsTo('App\Models\Learn\Course', 'course_id', 'id');
    }  

    public function exam_type(){
        return $this->belongsTo('App\Models\Learn\ExamType', 'type_id', 'id');
    }
    
    public function lesson(){
        return $this->belongsTo('App\Models\Learn\Lesson', 'lesson_id', 'id');
    }  
    
    public function questions(){
        return $this->hasMany('App\Models\Learn\Question', 'exam_id', 'id');
    }  
    
    public function results(){
        return $this->hasMany('App\Models\Learn\Result', 'exam_id', 'id');
    }  

    public function tutors(){
        return $this->hasMany('App\Models\Learn\TutorExam', 'exam_id', 'id');
    }  
}
