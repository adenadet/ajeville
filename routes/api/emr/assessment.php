<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/assessments', 'as'=>'api.emr.assessments.'], function () {
    Route::get('/assess/assigned',        'MainController@assessment')->name('assigned');
    Route::get('/assess/dom_assigned',    'MainController@dom_assessment')->name('dom_assigned');

    Route::apiResources([
        '/assess'             => 'MainController',
        '/types'              => 'TypeController',
    ]);
});