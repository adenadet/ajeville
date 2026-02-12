<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/radiology', 'as'=>'api.emr.radiology.'], function () {

    Route::get( '/services/initials',            'ServiceController@initials')->name('services.initials');
    
    Route::apiResources([
        'dashboard'         => 'DashboardController',
        'queues'            => 'QueueController',
        'services'          => 'ServiceController',
    ]);
});