<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/radiology', 'as'=>'api.emr.radiology.'], function () {
    Route::apiResources([
        'dashboard'         => 'DashboardController',
        'queues'            => 'QueueController',
    ]);
});