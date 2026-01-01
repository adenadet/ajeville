<?php

namespace App\Http\Controllers\Api\Learn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Learn\Category;
use App\Models\Learn\CertificateType;
use App\Models\Learn\Course;
use App\Models\Learn\Exam;
use App\Models\Learn\ExamType;
use App\Models\Learn\Lesson;
use App\Models\Learn\Question;
use App\Models\Learn\QuestionType;
use App\Models\Learn\SubCategory;
use App\Models\Learn\UserCourse;
use App\Models\Learn\UserExam;
use App\Models\Learn\UserLesson;


use App\Models\User;

class StdCourseController extends Controller
{
    public function initialize($id)
    {
        $lesson = Lesson::find($id);
        $user_course = UserCourse::where('user_id', '=', auth('api')->id())->where('course_id', '=', $lesson->course_id)->first();
        if (is_null($user_course)){return response()->json(['message' => 'Error']);}
        else{
            $user_course->status = 2;
            $user_course->level = 0;
            $user_course->save();
        }
        
        $user_lesson = UserLesson::create([
            'user_id' => auth('api')->id(),
            'lesson_id' => $id,
            'course_id' => $lesson->course_id,
            'user_start_time' => date('Y-m-d H:i:s'), 
            'status' => 2,
        ]);

        return response()->json([
            'message' => 'Done',
            'user_lesson' => $user_lesson, 
            ]);
    }

    public function index()
    {
        return response()->json([
            'courses' => UserCourse::where('user_id', '=', auth('api')->id())->with('course.lessons.exam')
            ->orderBy('expiry_date', 'DESC')->paginate(5),
        ]);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required', 
            'category_id' => 'required', 
            'sub_category_id' => 'required', 
            'description' => 'required', 
            'price' => 'required|numeric', 
            'exam_type_id' => 'required|numeric', 
            'certificate_type_id' => 'required|numeric', 
        ]);

        $sub_category = SubCategory::find($request['sub_category_id']);
        $course = Course::create([
            'name' => $request['name'], 
            'category_id' => $sub_category->category_id, 
            'description' => $request['description'], 
            'sub_category_id' => $sub_category->id, 
            'price' => $request['price'], 
            'exam_type_id' => $request['exam_type_id'], 
            'certificate_type_id' => $request['certificate_type_id'], 
        ]);

        $lesson = Lesson::create([
            'name' => 'Sample Course',
            'course_id' => $course->id, 
            'content' => 'Kindly edit to need', 
            'lesson_type_id' => 1,
            'document' => null,
            'video' => null,
            'pdf' => null,
            'serial_number' => null,
        ]);

        //Send response to server
        return response()->json([
            'message' => 'New Course: '.$course->name.' has been created successfully',
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'courses' => Course::with('assignees.user')->with('tutors.tutor.department')->with('category.sub_categories')->with('lessons.course')->with('sub_category')->orderBy('name', 'ASC')->paginate(20),       
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users' => User::all(),
            ]);
    }

    public function show($id)
    {
        $course = UserCourse::where('user_id', '=', auth('api')->id())->where('course_id', '=', $id)->with('course.lessons')->first();
    
        return response()->json([
            'course' => $course,
        ]);
    }

    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name' => 'required', 
            'category_id' => 'required', 
            'sub_category_id' => 'required', 
            'description' => 'required', 
            'price' => 'required|numeric', 
            'exam_type_id' => 'required|numeric', 
            'certificate_type_id' => 'required|numeric', 
            //'tutors' => 'required|array|min:2',
            //'tutors.*' => 'required|numeric',
        ]);

        $sub_category = SubCategory::find($request['sub_category_id']);
        
        //Find the Course
        $course = Course::find($id);
                
        $course->name = $request['name']; 
        $course->category_id = $sub_category->category_id;
        $course->description = $request['description']; 
        $course->sub_category_id = $sub_category->id; 
        $course->price = $request['price'];
        $course->exam_type_id = $request['exam_type_id'];
        $course->certificate_type_id = $request['certificate_type_id'];
        
        //Save the course
        $course->save();
        
        //Send response to server
        return response()->json([
            'message' => 'New Course: '.$course->name.' has been created successfully',
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'courses' => Course::with('assignees.user')->with('tutors.tutor.department')->with('category.sub_categories')->with('lessons')->with('sub_category')->orderBy('name', 'ASC')->paginate(20),       
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users' => User::all(),
        ]);

    }

    public function destroy($id)
    {
        $course = Course::find($id);

        $course->delete();

        return response()->json([
            'message' => 'New Course: '.$course->name.' has been created successfully',
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'courses' => Course::with('assignees.user')->with('tutors.tutor.department')->with('category.sub_categories')->with('lessons.course')->with('sub_category')->orderBy('name', 'ASC')->paginate(20),       
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users' => User::all(),
            ]);
    }
}
