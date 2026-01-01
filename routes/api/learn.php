<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'learn'], function () {
    
    Route::get('/std_courses/initialize/{id}', 'StdCourseController@initialize')->name('std_courses.initialize');
    Route::get('/std_lessons/complete/{id}', 'StdLessonController@complete')->name('std_lessons.complete');
    Route::get('/assign_tutors', 'AssignUserController@tutors')->name('assign_tutor');
    Route::post('/assign_tutors', 'AssignUserController@tutor_assign')->name('assign_tutor');
    Route::post('/lessons/fileUpload', 'LessonController@fileUpload')->name('lesson.fileUpload');
    Route::post('/lessons/modify', 'LessonController@modify')->name('lesson.modify');
    Route::post('/course/assign_tutors', 'CourseController@assign_tutors')->name('course.assign_tutors');
    Route::post('/course/assign_users', 'CourseController@assign_users')->name('course.assign_users');
    
    Route::apiResources([
        '/assign_users'     => 'AssignUserController',
        '/categories'       => 'CategoryController',
        '/courses'          => 'CourseController',
        '/dashboard'        => 'DashboardController',
        '/departments'      => 'DepartmentController',
        '/exams'            => 'ExamController',
        '/exam_results'     => 'ExamResultController',
        '/exam_types'       => 'ExamTypeController',
        '/lessons'          => 'LessonController',
        '/options'          => 'OptionController',
        '/questions'        => 'QuestionController',
        '/student_courses'  => 'StdCourseController',
        '/student_exams'    => 'StdExamController',
        '/student_lessons'  => 'StdLessonController',
        '/sub_categories'   => 'SubCategoryController',
        '/tutor_courses'    => 'TutCourseController',
        '/tutor_exams'      => 'TutExamController',
        '/user_courses'     => 'UserCourseController',
    ]);
});