<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/settings', 'as'=>'api.emr.settings.'], function () {

    Route::get( '/services/initials',            'ServiceController@initials')->name('services.initials');
    
    Route::apiResources([
        'services'          => 'ServiceController',
    ]);
});