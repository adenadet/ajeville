<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'operations'], function () {
    
    Route::get( '/branches/initials',            'BranchController@initials')->name('branches.initials');
    Route::get( '/branches/get_cookie',          'BranchController@get_cookie')->name('branches.get_cookie');
    Route::post('/branches/set_cookie',          'BranchController@set_cookie')->name('branches.set_cookie');
    Route::get( '/departments/initials',         'DepartmentController@initials')->name('departments.initials');
    Route::put( '/price_lists/import/{id}',      'PriceListController@import')->name('price_lists.import');
    Route::get( '/price_lists/initials',         'PriceListController@initials')->name('price_lists.initials');
    Route::get( '/services/initials',            'ServiceController@initials')->name('services.initials');
    Route::get( '/services/lists/{id}',          'ServiceController@lists')->name('services.lists');

    Route::apiResources([
        '/branches'             => 'BranchController',
        '/dashboard'            => 'DashboardController',
        '/departments'          => 'DepartmentController',
        '/price_lists'          => 'PriceListController',
        '/services'             => 'ServiceController',
        '/service_types'        => 'ServiceTypeController',
    ]);
});