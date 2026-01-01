<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/domiciliary', 'as'=>'api.emr.domiciliary.'], function () {
    Route::put('/requests/assign/{id}',         'RequestController@assign')->name('requests.assign');
    Route::put('/requests/confirm/{id}',        'RequestController@confirm')->name('requests.confirm');
    Route::get('/requests/pending',             'RequestController@pending')->name('requests.pending');
    Route::get('/batch_assigns/assigned',       'BatchAssignController@assigned')->name('batch_assign.assigned');
    Route::put('/batch_assigns/confirm/{id}',   'BatchAssignController@confirmArrival')->name('batch_assign.confirm-arrival');
    Route::post('/batch_assigns/search',        'BatchAssignController@search')->name('batch_assign.search');
    Route::get('/assessments/assigned',         'RequestController@assessment')->name('assessments.assigned');

    Route::apiResources([
        'batch_assigns'     => 'BatchAssignController',
        'batch_tasks'       => 'BatchController',
        'requests'          => 'RequestController',
        'shift_types'       => 'ShiftTypeController',
    ]);
});