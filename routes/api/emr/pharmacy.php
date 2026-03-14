<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/pharmacy', 'as'=>'api.emr.pharmacy.'], function () {
    Route::post('/drugs/all'     , 'DrugController@all')->name('drugs.all');
    /*Route::post('/drug_items/import'     , 'DrugItemController@import')->name('drug_items.import');
    Route::post('/drug_items/initials'     , 'DrugItemController@initials')->name('drug_items.initials');*/
    Route::get('/products/search', 'ProductController@search')->name('products.search');
    
    Route::apiResources([
        '/dashboard'        => 'DashboardController',
        '/drugs'            => 'DrugController',
        '/drug_forms'       => 'DrugFormController',
        '/drug_items'       => 'DrugItemController',
        '/prescriptions'    => 'PrescriptionController',
        '/products'         => 'ProductController',
    ]);
});