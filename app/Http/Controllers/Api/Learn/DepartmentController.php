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
use App\Models\Department;
use App\Models\User;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('users')->get();
        $users = User::all();

        return response()->json([
            'departments' => $departments,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        return response()->json([
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'courses' => Course::with('assignees.user')->with('tutors.tutor.department')->with('category.sub_categories')->with('lessons.course')->with('sub_category')->orderBy('name', 'ASC')->paginate(20),       
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users' => User::all(),
            ]);
    }
}
