<?php 

use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'crm'], function () {

    Route::get( '/customers/initials',                  'CustomerController@initials')->name('customers.initials');
    Route::post('/customers/import',                    'CustomerController@import')->name('customers.import');
    
    Route::apiResources([
        'categories'                    => 'CategoryController',
        'contacts'                      => 'ContactController',
        'customers'                     => 'CustomerController',
        'dashboard'                     => 'DashboardController',   
    ]);
});