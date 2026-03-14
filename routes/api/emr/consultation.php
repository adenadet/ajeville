<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/consultations', 'as'=>'api.emr.consultations.'], function () {
    Route::get('/consultants/doctor_queue',        'ConsultantController@doctor_queue')->name('consultants.doctor_queue');   
    Route::get('/consultants/initials',            'ConsultantController@initials')->name('consultants.initials');
    Route::get('/consultants/nurse_queue',         'ConsultantController@nurse_queue')->name('consultants.nurse_queue');
    Route::get('/consultants/start/{id}',          'ConsultantController@start')->name('consultants.start');
    Route::get('/consultants/my_past_consultations', 'ConsultantController@my_past_consultations')->name('consultants.my_past_consultations');

    Route::apiResources([
        '/consultants'       => 'ConsultantController',
        '/dashboard'         => 'DashboardController',
        '/request_templates' => 'RequestTemplateController',
    ]);
});