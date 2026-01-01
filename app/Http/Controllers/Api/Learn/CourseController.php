<?php

namespace App\Http\Controllers\Api\Learn;

use App\Http\Controllers\Controller;
use App\Http\Traits\Learn\CourseTrait;
use App\Http\Traits\Learn\LearningTrait;
use App\Models\Learn\Category;
use App\Models\Learn\Course;
use App\Models\Learn\Certificate;
use App\Models\Learn\CertificateType;
use App\Models\Learn\Exam;
use App\Models\Learn\ExamType;
use App\Models\Learn\Lesson;
use App\Models\Learn\SubCategory;
use App\Models\Learn\TutorCourse;
use App\Models\Learn\UserCourse;
use App\Models\Department;
use App\Models\User;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class CourseController extends Controller
{
    use CourseTrait, LearningTrait;

    public function assign_tutors(Request $request)
    {
        $this->validate($request, [
            'course_id'         => 'required', 
            'users'             => 'required|array',
            'users.*'           => 'required|numeric|distinct',
        ]);

        foreach ($request->input('users') as $user){
            $tutor_course = TutorCourse::where('course_id', '=', $request->input('course_id'))->where('tutor_id', '=', $user)->first();

            if ($tutor_course === null){
                $tutorcourse = TutorCourse::create([
                    'course_id'     => $request->input('course_id'),
                    'tutor_id'      => $user,
                    'created_by'    => auth('api')->id(),
                    /* 'assigned_date' => date('Y-m-d H:i:s'),
                    'start_date'    => $request->input('start_date'),
                    'expiry_date'   => $request->input('expiry_date'), */
                ]);
            }
        }
        
        return response()->json([
            'categories'        => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'course'            => Course::where('id', '=', $request->input('course_id'))->with('assignees.user')->with('tutors.tutor')->with('lessons.exam')->with('category')->with('sub_category')->first(),
            'departments'       => Department::select('id', 'name')->with('users')->get(),
            'exam_types'        => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users'             => User::all(),
        ]);
    }

    public function assign_users(Request $request)
    {
        $this->validate($request, [
            'course_id'         => 'required', 
            'users'             => 'required|array',
            'users.*'           => 'required|numeric|distinct',
            //'start_date'        => 'required', 
            //'end_date'          => 'required',
        ]);

        foreach ($request->input('users') as $user){
            $user_course = UserCourse::where('course_id', '=', $request->input('course_id'))->where('user_id', '=', $user)->first();

            if ($user_course === null){
                $usercourse = UserCourse::create([
                    'course_id'     => $request->input('course_id'),
                    'user_id'       => $user,
                    'created_by'    => auth('api')->id(),
                    'assigned_date' => date('Y-m-d H:i:s'),
                    'start_date'    => $request->input('start_date'),
                    'expiry_date'   => $request->input('expiry_date'),
                ]);
            }
        }
       
        return response()->json([
            'categories'        => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'course'            => Course::where('id', '=', $request->input('course_id'))->with('assignees.user')->with('tutors.tutor')->with('lessons.exam')->with('category')->with('sub_category')->first(),
            'departments'       => Department::select('id', 'name')->with('users')->get(),
            'exam_types'        => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users'             => User::all(),
        ]);
    }

    public function index()
    {
        return response()->json([
            'courses' => $this->learn_course_get_all($_GET['status'] ??'all', $_GET, true, true, $_GET['page'] ?? 1),
        ]);
    }

    public function show($id)
    {
        $course = $this->learn_course_get_by(null, $id, true);
        //Course::where('id', '=', $id)->with('assignees.user')->with('tutors.tutor')->with('lessons.exam')->with('category')->with('sub_category')->first(),
            
        return response()->json([
            'course' => $course,
            /*'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'course' => Course::where('id', '=', $id)->with('assignees.user')->with('tutors.tutor')->with('lessons.exam')->with('category')->with('sub_category')->first(),
            'departments' => Department::select('id', 'name')->with('users')->get(),
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'tutors' => User::role('Tutor')->get(),
            'users' => User::all(),*/
        ], is_string($course) ? 404 : 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required', 
            'category_id'       => 'required', 
            'sub_category_id'   => 'required', 
            'description'       => 'nullable|string', 
            'price'             => 'required|numeric', 
            'exam_type_id'      => 'required|numeric', 
            'certificate_type_id' => 'required|numeric',
            'lessons'           => 'required|numeric',
        ]);

        $sub_category = SubCategory::find($request['sub_category_id']);
        //Create the Course
        $course = Course::create([
            'name' => $request['name'], 
            'category_id' => $sub_category->category_id, 
            'description' => $request['description'], 
            'sub_category_id' => $sub_category->id, 
            'price' => $request['price'], 
            'exam_type_id' => $request['exam_type_id'], 
            'certificate_type_id' => $request['certificate_type_id'], 
        ]);

        if ($request['exam_type_id'] == 4 || $request['exam_type_id'] == 5){$lesson_type_id = 1;}
        else{$lesson_type_id = 2;}
        
        for ($i = 1; $i <= $request->input('lessons'); $i++){
            $lesson = Lesson::create([
                'name' => 'Sample Module '.$i,
                'course_id' => $course->id, 
                'content' => 'Kindly edit to need', 
                'lesson_type_id' => $lesson_type_id,
                'document' => null,
                'video' => null,
                'pdf' => null,
                'serial_number' => null,
            ]);
        }
        
        return response()->json([
            'message' => 'New Course: '.$course->name.' has been created successfully',
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'courses' => Course::with('assignees.user')->with('tutors.tutor.department')->with('category.sub_categories')->with('lessons.course')->with('sub_category')->orderBy('name', 'ASC')->paginate(20),       
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users' => User::all(),
        ]);
    }

    
    public function update(Request $request, $id)
    {
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
        
        //Find the Course
        $course = Course::find($id);
                
        $course->name               = $request['name']; 
        $course->category_id        = $sub_category->category_id;
        $course->description        = $request['description']; 
        $course->sub_category_id    = $sub_category->id; 
        $course->price              = $request['price'];
        $course->exam_type_id       = $request['exam_type_id'];
        $course->certificate_type_id = $request['certificate_type_id'];
        
        //Save the course
        $course->save();
        
        //Send response to server
        return response()->json([
            'message' => 'New Course: '.$course->name.' has been created successfully',
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'courses' => Course::with('assignees.user')->with('tutors.tutor.department')->with('category.sub_categories')->with('lessons.exam')->with('sub_category')->orderBy('name', 'ASC')->paginate(20),       
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
