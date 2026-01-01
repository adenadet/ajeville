<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/laboratory', 'as'=>'api.emr.laboratory.'], function () {
    
    //Route::get('/branches/initials', 'BranchController@initials')->name('branches.initials');
    //Route::get('branches/get_cookie',   'BranchController@get_cookie')->name('branches.get_cookie');
    //Route::post('branches/set_cookie',  'BranchController@set_cookie')->name('branches.set_cookie');
    //Route::get('/departments/initials', 'DepartmentController@initials')->name('departments.initials');
    //Route::get('/price_lists/initials', 'PriceListController@initials')->name('price_lists.initials');
    Route::get('/requests/initials',     'RequestController@initials')->name('laboratory.iniytials');

    Route::get('/dashboard',                            'RequestController@dashboard')->name('dashboard');
    Route::get('/requests/collect/{id}',                'RequestController@collect')->name('requests.collect');
    Route::get('/requests/insurance',                   'RequestController@insurance')->name('requests.insurance');

    Route::apiResources([
        'bottles'           => 'BottleController',
        'dashboard'         => 'DashboardController',
        'queues'            => 'QueueController',
        'requests'          => 'RequestController',
        'services'          => 'ServiceController',
    ]);
    
});