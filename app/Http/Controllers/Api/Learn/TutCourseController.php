<?php

namespace App\Http\Controllers\Api\Learn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Learn\Category;
use App\Models\Learn\Course;
use App\Models\Learn\Certificate;
use App\Models\Learn\CertificateType;
use App\Models\Learn\Exam;
use App\Models\Learn\ExamType;
use App\Models\Learn\Lesson;
use App\Models\Learn\SubCategory;
use App\Models\Learn\TutorCourse;
use App\Models\User;

class TutCourseController extends Controller
{
    public function index()
    {
        $tut_courses = TutorCourse::where('tutor_id', '=', auth('api')->id())->with('course')->get();
        $tut_course_id = TutorCourse::select('course_id')->where('tutor_id', '=', auth('api')->id())->get();
        $courses = Course::whereIn('id', $tut_course_id)->with('assignees.user')->with('tutors.tutor.department')->with('category.sub_categories')->with('lessons.course')->with('sub_category')->orderBy('name', 'ASC')->paginate(20);


        return response()->json([
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'courses' => $courses,
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users' => User::all(),
            ]);

    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $course = Course::where('id', '=', $id)->with('assignees')->with('tutors')->with('lessons')->first();
        $tut_courses = TutorCourse::where('tutor_id', '=', auth('api')->id())->with('course')->get();
        $tut_course_id = TutorCourse::select('course_id')->where('tutor_id', '=', auth('api')->id())->get();
        $courses = Course::whereIn('id', $tut_course_id)->with('assignees.user')->with('tutors.tutor.department')->with('category.sub_categories')->with('lessons.course')->with('sub_category')->orderBy('name', 'ASC')->paginate(20);

        return response()->json([
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'course' => $course,
            'courses' => $courses,
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users' => User::all(),
        ]);

    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
