<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/admissions', 'as'=>'api.emr.admissions.'], function () {
    
    Route::get('/bed_assignments/initials',          'BedAssignmentController@initials')->name('bed_assignments.initials');
    Route::put('/requests/{id}/admit',    'RequestController@admit')->name('requests.admit');    
    Route::get('/requests/{id}/confirm',  'RequestController@confirm')->name('requests.confirm');    
    Route::get('/requests/initials',      'RequestController@initials')->name('requests.initials');
    Route::put('/requests/{id}/prechecks','RequestController@prechecks')->name('requests.prechecks');
    Route::get('/rooms/initials',         'RoomController@initials')->name('rooms.initials');
    Route::get('/room_types/initials',    'RoomTypeController@initials')->name('room_types.initials');
    Route::get('/services/initials',      'ServiceController@initials')->name('services.initials');
    Route::get('/wards/initials',         'WardController@initials')->name('wards.initials');
    
    Route::apiResources([
        '/beds'              => 'BedController',
        '/bed_assignments'  => 'BedAssignmentController',
        '/categories'       => 'CategoryController',
        '/dashboard'        => 'DashboardController',
        '/requests'         => 'RequestController',
        '/rooms'            => 'RoomController',
        '/room_types'       => 'RoomTypeController',
        '/services'         => 'ServiceController',
        '/wards'            => 'WardController',
    ]);
});