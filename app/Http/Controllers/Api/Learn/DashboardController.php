<?php

namespace App\Http\Controllers\Api\Learn;

use App\Http\Controllers\Controller;
use App\Http\Traits\Learn\CourseTrait;
use App\Http\Traits\Learn\LearningTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    use CourseTrait, LearningTrait;
    public function index()
    {
        switch($_GET['type']){
            case 'admin':
                return response()->json([
                    'courses' => $this->learn_course_get_all('active', null, true, true, null),
                ]);
            case 'student':
                return response()->json([
                    'user_courses' => $this->learn_user_course_get_all($_GET['specific'] ?? 'mine', null, true, true),
                ]);
            case 'tutor':
                return response()->json([
                    'courses' => $this->learn_tutor_course_get_all('active', null, true, true),
                ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
