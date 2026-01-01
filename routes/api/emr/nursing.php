<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/nursing', 'as'=>'api.emr.nursing.'], function () {
    Route::get('/patient_tasks/dom/{id}',      'PatientTaskController@domiciliary')->name('patient_tasks.domiciliary');
    Route::get('/tasks/initials',              'TaskController@initials')->name('tasks.initials');
    
    Route::apiResources([
        'patient_tasks'  => 'PatientTaskController',
        'tasks'          => 'TaskController',
        'vitals'         => 'VitalController',
    ]);
});