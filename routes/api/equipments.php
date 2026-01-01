<?php

use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'equipments'], function () {

    Route::post('/assets/{id}/assign', 'AssetController@assign')->name('assets.assign');
    Route::get('/assets/initials', 'AssetController@initials')->name('assets.initials');
    Route::get('/assets/report', 'AssetController@report')->name('assets.report');
    Route::post('/assets/{id}/return', 'AssetController@return')->name('assets.return');

    Route::apiResources([
        'assets'                => 'AssetController',
        'asset_types'           => 'AssetTypeController',
        'assignment_register'   => 'AssignmentRegisterController',
        'dashboard'             => 'DashboardController',
        'locations'             => 'LocationController',
    ]);
});