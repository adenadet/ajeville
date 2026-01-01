<?php

namespace App\Http\Controllers\Api\Learn;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Learn\Course;
use App\Models\Learn\Exam;
use App\Models\Learn\ExamType;
use App\Models\Learn\Option;
use App\Models\Learn\Question;
use App\Models\Learn\QuestionType;
use App\Models\Learn\Result;

class ExamResultController extends Controller
{
    public function index()
    {
        return response()->json([
            'results' => Result::where('exam_id', '=', $_GET['r'])->with('exam')->with('user')->paginate(5),       
        ]);
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        return response()->json([
            'result' => Result::where('id', '=', $id)->with('exam.questions')->with('answers.question.options')->with('user')->first(),
        ]);
        /*
        return response()->json([
            'categories' => Category::select('id', 'name',)->with('sub_categories')->orderBy('name', 'ASC')->get(),       
            'certificate_types' => CertificateType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'course' => Course::where('id', '=', $id)->with('assignees.user')->with('tutors.tutor')->with('lessons.exam')->with('category')->with('sub_category')->first(),
            'departments' => Department::select('id', 'name')->with('users')->get(),
            'exam_types' => ExamType::select('id', 'name')->orderBy('name', 'ASC')->get(),
            'users' => User::all(),
        ]);*/

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
