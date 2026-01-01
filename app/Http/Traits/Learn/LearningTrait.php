<?php
namespace App\Http\Traits\Learn;

use App\Models\Learn\Course;
use App\Models\Learn\Exam;
use App\Models\Learn\ExamResult;
use App\Models\Learn\Lesson;
use App\Models\Learn\UserCourse;
use App\Models\Learn\UserExam;
use App\Models\Learn\UserLesson;
use App\Models\Learn\TutorCourse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait LearningTrait{
    public function learn_user_course_complete($id){}
    public function learn_user_course_create($data){
        DB::beginTransaction();

        try{
            //Check that this user has not already been assigned this course for this period
            $assigned_period = UserCourse::where('user_id', '=', $data['user_id'])->where('course_id', '=', $data['course_id'])->whereDate('end_date', '>=', $data['expiry_date'])->first();
            
            if ($assigned_period){
                return "User has already been assigned this course for this period";    
            }
            else{
                $query = UserCourse::create([
                    'user_id' => $data['user_id'], 
                    'course_id' => $data['course_id'],
                    'level' => $data['level'] ?? 1,
                    'assigned_date' => $data['assigned_date'] ?? date('Y-m-d H:i:s'),
                    'start_date' => $data['start_date'],
                    'expiry_date' => $data['expiry_date'],
                    'user_start_time' => $data['user_start_time'] ?? null,
                    'user_finish_time' => $data['user_finish_time'] ?? null,
                    'status' => $data['stauts'] ?? 1,
                    'created_by' => auth('api')->id() ?? Auth::id(),
                    'updated_by' => auth('api')->id() ?? Auth::id(),
                ]);
                DB::commit();
                $this->log_user_activity('Learn User Course create', $query->id, true);
                return $query;
            }
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn User Course create', null, false);
            return $e->getMessage();
        }
    }
    public function learn_user_course_deactivate($id){
        DB::beginTransaction();

        try{
            $query = UserCourse::findOrFail($id);

            if($query->status == 1){
                $query->status = 0;
                $query->deleted_by = Auth::id() ?? auth('api')->id();
                $query->deleted_at = date('Y-m-d H:i:s'); 
            
            }
            else {
                $query->status = 1;
                $query->deleted_by = null;
                $query->deleted_at = null; 
            }

            $query->updated_by = Auth::id() ?? auth('api')->id();
            $query->save();

            DB::commit();
            $this->log_user_activity('Learn User Course Deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn User Course Deactivate', $id, false);
            return $e->getMessage();
        }
    }
    public function learn_user_course_get_all($type, $specific, $detailed, $paginated){
        $query = UserCourse::query();

        switch($type){
            case 'all':
                $query = $query->withTrashed();
            break;
            case 'active':
                $query = $query->where('status', '=', 1)->whereDate('start_date', '>=', date('Y-m-d'))->whereDate('expiry_date', '<=', date('Y-m-d'));
            break;
            case 'in_active':
                $query = $query->where('status', '=', 0)->orWhereDate('start_date', '<', date('Y-m-d'))->orWhereDate('expiry_date', '<', date('Y-m-d'));
            break;
            case 'mine':
                $query = $query->where('user_id', '=', (Auth::id() ?? auth('api')->id()));
            break;
            case 'my_active':
                $query = $query->where('user_id', '=', (Auth::id() ?? auth('api')->id()))->where('status', '=', 1)->whereDate('start_date', '>=', date('Y-m-d'))->whereDate('expiry_date', '<=', date('Y-m-d'));
            break;
        }

        $query = $detailed ? $query->with(['course', 'user', 'creator', 'updater', 'deleter']) : $query->select('id', 'user_id', 'course_id');
        $query = $query->orderBy('start_date', 'DESC');
        $query = $paginated ? $query->paginate(25) : $query->get();

        return $query;
    }
    public function learn_user_course_get_by($type, $id, $detailed){
        try{
            $query = UserCourse::where('id', '=', $id);
            $query = $detailed ? $query->with('course.lessons', 'creator', 'deleter', 'updater', 'user') : $query->select('id', 'name', 'course_id', 'user_id');
            return $query->firstOrFail();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }

    public function learn_user_course_update($data, $id){
        DB::beginTransaction();

        try{
            $query = UserCourse::findOrFail($id);

            $query->user_id = $data['user_id'] ?? $query->user_id;
            $query->course_id = $data['course_id'] ?? $query->course_id;
            $query->level = $data['level'] ?? $query->level;
            $query->assigned_date = $data['assigned_date'] ?? $query->assigned_date;
            $query->start_date = $data['start_date'] ?? $query->start_date;
            $query->expiry_date = $data['expiry_date'] ?? $query->expiry_date;
            $query->user_start_time = $data['user_start_time'] ?? $query->user_start_time;
            $query->user_finish_time = $data['user_finish_time'] ?? $query->user_finish_time;
            $query->status = $data['stauts'] ?? $query->status;
            
            $query->save();
            
            DB::commit();
            $this->log_user_activity('Learn User Course Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn User Course Update', $id, false);
            return $e->getMessage();
        }
    }

    public function learn_user_exam_create($data){
        DB::beginTransaction();

        try{
            //Check that the exam exists
            $exam = Exam::where('id', '=', $data['exam_id'])->firstOrFail();
            
            //Check if the exam has a course
            if ($exam->course_id !== null){
                $user_course = UserCourse::where('user_id', '=', $data['user_id'])->where('course_id', '=', $exam->course_id)->whereNotIn('status', [UserCourse::StatusQueried, UserCourse::StatusCompleted])->orderBy('expiry_date', 'DESC')->first();

                //Assign course to User if not already done
                if (!$user_course){
                    $user_course = UserCourse::create([
                        'user_id' => $data['user_id'], 
                        'course_id' => $exam->course_id,
                        'level' => 1,
                        'assigned_date' => date('Y-m-d H:i:s'),
                        'start_date' => $data['start_date'],
                        'expiry_date' => $data['expiry_date'],
                        'user_start_time' => $data['user_start_time'] ?? null,
                        'user_finish_time' => $data['user_finish_time'] ?? null,
                        'status' => $data['stauts'] ?? 1,
                        'created_by' => auth('api')->id() ?? Auth::id(),
                        'updated_by' => auth('api')->id() ?? Auth::id(),
                    ]);
                }
            }

            //Create User exam
            $query = UserExam::create([
                'user_id'           => $data['user_id'],
                'exam_id'           => $exam->id,
                'course_id'         => $exam->course_id ?? null,
                'lesson_id'         => $exam->lesson_id ?? null,
                'assigned_date'     => $user_course->assigned_date ?? $data['assigned_date'],
                'start_date'        => $user_course->start_date ?? $data['start_date'],
                'expiry_date'       => $user_course->expiry_date ?? $data['expiry_date'],
                'user_start_time'   => null,
                'user_finish_time'  => NULL,
                'started'           => 0,
            ]);

            DB::commit();
            $this->log_user_activity('Learn User Exam Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn User Exam Create', null, false);
            return $e->getMessage();
        }
    }


    
    public function learn_user_lesson_complete($user, $id){
        $lesson = Lesson::find($id);
        $user_lesson = UserLesson::where('user_id', '=', $user->id)
                        ->where('lesson_id', '=', $id)->with('lesson.exam')->first();
        $user_course = UserCourse::where('user_id', '=', $user->id)
                        ->where('course_id', '=', $lesson->course_id)->with('course.lessons')->first();

        //If Lesson or Course are absent, stop completion process
        if (is_null($user_course) || is_null($user_lesson)){
            return ['message' => 'Error', 'course' => $user_course];
        }

        //If the lesson has an exam
        if (!is_null($user_lesson->lesson->exam)){ //Check if the exam has been passed by the user
            $user_exam = UserExam::where('user_id', '=', $user->id)->where('exam_id', '=', $lesson->exam->id)
                        //->where('start_date', '>=', date('Y-m-d'))->where('expiry_date', '<=', date('Y-m-d'))
                        ->first();
            //If not yet created, create one for User
            if (is_null($user_exam)){ 
                $user_exam = UserExam::create([
                    'user_id' => $user->id, 
                    'exam_id' => $lesson->exam->id, 
                    'course_id' => $lesson->course_id, 
                    'lesson_id' => $lesson->id, 
                    'assigned_date' => $user_lesson->assigned_date, 
                    'status' => 0,
                ]);

                $message = "You have not completed the exam for this lesson. You can't continue to next lesson";
                $icon = "warning";
                return ['icon'=> $icon, 'message' => $message, 'course' => $user_course];
                }
            else if ($user_exam->status == 3){ // If User has been passed exam
                $user_lesson->status = 3;
                $user_lesson->save();
                 
                if ($user_course->course->lessons[$user_course->level]->id == $lesson->id){
                    $user_course->level++;
                    $user_course->save();

                    $next_lesson = UserLesson::create([
                        'user_id' => $user->id,
                        'course_id' => $user_course->course->id,
                        'lesson_id' => $user_course->course->lessons[$user_course->level]->id,
                        'start_date' => date('Y-m-d'),
                        'status' => 3,
                    ]);
                }

                $message = "Exam has been passed. You can continue to next lesson";
                $icon = "success";
                return ['icon'=> $icon, 'message' => $message, 'course' => $user_course];
            } 
            else if ($user_exam->status == 6){ //If Exam was skipped
                if ($user_course->course->lessons[$user_course->level]->id == $lesson->id){
                    $user_course->level++;
                    $user_course->save();

                    $next_lesson = UserLesson::create([
                        'user_id' => $user->id,
                        'course_id'=>  $user_course->course->id,
                        'lesson_id' => $user_course->course->lessons[$user_course->level]->id,
                        'start_date' => date('Y-m-d'),
                        'status' => 3,
                    ]);
                }
                $message = "Exam has been skipped has you have failed multiple times. You can continue to next lesson";
                $icon = "success";
                return ['icon'=> $icon, 'message' => $message, 'course' => $user_course];
            }
            else{//Check number of trials
                $trials = ExamResult::where('exam_id', '=', $lesson->exam->id)->where('user_id', '=', $user->id)->get();
                if ($trials->count() >= $user_lesson->lesson->exam->trials){
                    $user_exam->status = 6; 
                    $user_exam->save(); 
                    $user_lesson->status = 3; 
                    $user_lesson->user_finish_time = date('Y-m-d H:i:s'); //$user_lesson->exam_id = 0; 
                    $user_lesson->save(); 

                    //Update User Course to allow next level
                    
                    if ($user_course->course->lessons[$user_course->level]->id == $lesson->id){
                        $user_course->level++;
                        $user_course->save();
                        //Create a new UserLesson, so that the user can read the next lesson
                        $new_user_lesson = UserLesson::create([
                            'course_id' => $user_course->course->id,
                            'user_id' => $user->id,
                            'lesson_id' => $user_course->course->lessons[$user_course->level]->id,
                            $user_course->course->lessons[$user_course->level]->id
                        ]); 
                    }
                    return ['icon'=> 'success', 'message' => 'Done', 'course' => $user_course];
                }
                else{
                    return ['icon'=> 'error', 'message' => 'Error', 'course' => $user_course];
                }
            }
        }
        else{
            
            $user_lesson->status = 3;
            $user_lesson->user_finish_time = date('Y-m-d H:i:s');
            $user_lesson->save();
            
            //Check if the 
            if ($user_course->course->lessons[$user_course->level]->id == $lesson->id){
                $user_course->level++;
                $user_course->save();
                //Create a new UserLesson, so that the user can read the next lesson
                $new_user_lesson = UserLesson::create([
                    'course_id' => $user_course->course->id,
                    'user_id' => $user->id,
                    'lesson_id' => $user_course->course->lessons[$user_course->level]->id,
                    $user_course->course->lessons[$user_course->level]->id
                ]); 
            }
            return ['icon'=> 'success', 'message' => 'Done', 'course' => $user_course, 'trial_count' => 0];
        }
    }

    public function learn_user_lesson_create($data){
        DB::beginTransaction();

        try{
            //Check if the user has already been assigned this course for this period
            $user_course = UserCourse::where([

            ]);

            $query = UserCourse::create([
                'user_id' => $data['user_id'],
                'lesson_id' => $data['lesson_id'],
                'course_id' => $data['course_id'], 
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => UserCourse::StatusAssigned,
                'created_by' => Auth::id() ?? auth('api')->id(),
                'updated_by' => Auth::id() ?? auth('api')->id(),
            ]);
            DB::commit();
            $this->log_user_activity('Learn User Course create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn User Course create', null, false);
            return $e->getMessage();
        }
    }

    public function learn_user_lesson_deactivate($id){
        DB::beginTransaction();
        try{
            $query = UserLesson::findOrFail($id);

            $query->deleted_by = Auth::id() ?? auth('api')->id();
            DB::commit();
            $this->log_user_activity('Learn User Course deactivate', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn User Course deactivate', $id, false);
            return $e->getMessage();
        }
    }

    public function learn_user_lesson_get_all($type, $specific, $detailed, $paginated){}

    public function learn_user_lesson_get_by($type, $id, $detailed){}

    public function learn_user_lesson_update($data, $id){
        DB::beginTransaction();

        try{
            $query = UserLesson::findOrFail($id);
            
            $query->user_id = $data['user_id'] ?? $query->user_id;
            $query->lesson_id = $data['lesson_id'] ?? $query->lesson_id;
            $query->course_id = $data['course_id'] ?? $query->course_id;
            $query->start_date = $data['start_date'] ?? $query->start_date;
            $query->end_date = $data['end_date'] ?? $query->end_date;
            $query->user_start_time = $data['user_start_time'] ?? $query->user_start_time;
            $query->user_finish_time = $data['user_finish_time'] ?? $query->user_finish_time;
            $query->status = $data['status'] ?? $query->status;
            $query->updated_by = Auth::id() ?? auth('api')->id();
            
            $query->save();

            DB::commit();
            $this->log_user_activity('Learn User Lesson Update', $id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn User Lesson Update', $id, false);
            return $e->getMessage();
        }
    }

    public function learn_get_lesson($id){
        $lesson = Lesson::where('id', '=', $id)->with('course')->with('exam')->first();
        $trials = 0;
        $message = '';
        $user_exam = '';

        //check if this user is supposed to be doing this course
        $user_course = UserCourse::where('course_id', '=', $lesson->course_id)
            ->where('user_id', '=', auth('api')->id())
            ->where('status', '<=', 2)->first();
            //->where('start_date', '>=', date('Y-m-d')) //Must have started
            //->where('expiry_date', '<=', date('Y-m-d')) //Must have not expired
            
        $user_lesson = UserLesson::where('lesson_id', '=', $id)
            ->where('user_id', '=', auth('api')->id())
            //->where('status', '<=', 3)
            ->with('lesson.exam')
            ->first();

        
        
        //Checks for Exam Done by User
        if (!is_null($lesson->exam)){
            //Check to see the User Exam
            $user_exam = UserExam::where('user_id', '=', auth('api')->id())->where('exam_id', '=', $lesson->exam->id)->first();
            //->where('start_date', '>=', date('Y-m-d')) //Must have started
            //->where('expiry_date', '<=', date('Y-m-d')) //Must have not expired

            //Check how many times 
            $trials = ExamResult::where('user_id', '=',  auth('api')->id())->where('exam_id', '=', $lesson->exam->id)->count();
        }
        else{}
        /*
        if (is_null($user_course)){
            if (is_null($user_lesson) && ($user_course->level == 0)){
                //Check if the course is a new course
                $user_lesson = UserLesson::create([
                    'course_id' => $user_course->course->id,
                    'user_id' => auth('api')->id(),
                    'lesson_id' => $lesson->id,
                    'assigned_date' => date('Y-m-d H:i:s'),
                ]);

                return[
                    'message' => "Go on",
                    'lesson' => $lesson,
                ]);
            }
            else{
                return[
                    'message' => "Don't go",
                    'lesson' => $lesson,
                ]);
            }
        }

        else if ((!is_null($user_course)) && (!is_null($user_lesson))){
            $lesson = Lesson::where('id', '=', $id)->with('course')->with('exam')->first(); //get the correct lesson
            if (!is_null($lesson->exam)){ //check if the lesson has a quiz,
                //-- Step 1: Check that the user has been assigned the exam --
                $user_exams = UserExam::where('user_id', '=', auth('api')->id())->where('exam_id', '=', $lesson->exam->id)->get();
                //->where('expiry_date', '>=', date('Y-m-d'))
                            
                //*-- Step 1b: If the User had not been previously assigned the exam, assign the exam to the user --*
                if (is_null($user_exams)){
                    $user_exam = UserExam::Create(
                    [
                        'assigned_date' => date('Y-m-d H:i:s'), 
                        'course_id' => $lesson->course_id,
                        'exam_id' => $lesson->exam->id, 
                        'expiry_date' => $lesson->course->expiry_date, 
                        'lesson_id' => $lesson->id,
                        'start_date' => $lesson->course->start_date,
                        'status' => 0,
                        'user_id' => auth('api')->id(),
                    ]);
                }
                //*--Step 2: Get the number of trials available trials left --*
                $trials = Result::where('exam_id', '=', $user_lesson->lesson->exam->id)->where('user_id', '=', auth('api')->id())->count();
            }
            $message = "Go On";
            }
        else{
            $message = "Don't go on";
            }
        */
        return [
            'course'    =>  $lesson->course,
            'lesson'    =>  $lesson,
            'message'   =>  $message,
            'page_title' => 'Student Portal',
            'trials'    =>  $trials,
            'user_exam' =>  $user_exam,
            'trial_count' => 0,
        ];
    } 

    public function learn_tutor_course_create($data){
        DB::beginTransaction();

        try{
            //$already_assigned = TutorCourse::where('course_id', '=', $data['course_id'])->
            $query = TutorCourse::create([

            ]);
            DB::commit();
            $this->log_user_activity('Learn Tutor Lesson Create', $query->id, true);
            return $query;
        }
        catch(Exception $e){
            DB::rollBack();
            $this->log_user_activity('Learn Tutor Lesson Create', null, false);
            return $e->getMessage();
        }
    }
    public function learn_tutor_course_deactivate($id){}
    public function learn_tutor_course_get_all($type, $specific, $detailed, $paginate){}
    public function learn_tutor_course_get_by($type, $id, $detailed){}

    public function learn_tutor_course_update($data, $id){}
}