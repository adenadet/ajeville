<?php

namespace App\Http\Controllers\Api\Learn;

use App\Http\Controllers\Controller;
use App\Http\Traits\Learn\LearningTrait;
use Illuminate\Http\Request;

class UserCourseController extends Controller
{
    use LearningTrait;

    public function destroy(string $id)
    {
        
    }

    public function index()
    {
        return response()->json([
            'user_courses' => $this->learn_user_course_get_all($_GET['specific'] ?? 'mine', $_GET, true, true),
        ]);
    }

    public function initials()
    {
        //
    }

    public function show(string $id)
    {
        $user_course = $this->learn_user_course_get_by(null, $id, true);

        return response()->json([
            'user_course' => $user_course,
        ], is_string($user_course) ? 404 : 200);
    }
    
    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }
}
