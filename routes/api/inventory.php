<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'inventory'], function () {
    Route::get( '/categories/initials',                 'CategoryController@initials')->name('categories.initials');
    Route::get( '/issues/initials',                     'IssueController@initials')->name('issues.initials');
    Route::post('/items/bulk_update',                   'ItemController@bulk_update')->name('items.bulk_update');
    Route::post('/items/import',                        'ItemController@import')->name('items.import');
    Route::get( '/items/initials',                      'ItemController@initials')->name('items.initials');
    Route::get( '/items/quick',                         'ItemController@quick_search')->name('items.quick_search');
    Route::get( '/items/quick_search',                  'ItemController@quick_search')->name('items.quick_search');
    Route::post('/items/report',                        'ItemController@report')->name('items.report');
    Route::post('/items/search_request',                'ItemController@search_request')->name('items.search_request');
    Route::post('/items/search',                        'ItemController@search')->name('items.search');
    Route::get('/stores/initials',                      'StoreController@initials')->name('stores.initials');
    Route::get('/store_items/batches/{store_id}/{item_id}', 'StoreItemController@batches')->name('store_items.batches');
    Route::get('/store_items/initials',                 'StoreItemController@initials')->name('store_items.initials');
    Route::put('/store_items/{id}/report/{type}',       'StoreItemController@report')->name('store_items.report');
    Route::get('/store_items/{id}/reset',               'StoreItemController@reset')->name('store_items.reset');
    Route::put('/store_items/search/{id}',              'StoreItemController@search')->name('store_items.search');
    Route::get('/transfer_orders/initials',             'TransferOrderController@initials')->name('transfer_orders.initials');
    Route::put('/transfer_orders/reject/{id}',          'TransferOrderController@reject')->name('transfer_orders.reject');

    Route::apiResources([
        'brands'            => 'BrandController',
        'dashboard'         => 'DashboardController',
        'categories'        => 'CategoryController',
        'classifications'   => 'ClassificationController',
        'fulfillments'      => 'FulfillmentController',
        'issues'            => 'IssueController',
        'items'             => 'ItemController',
        'item_types'        => 'ItemTypeController',
        'stores'            => 'StoreController',
        'store_items'       => 'StoreItemController',
        'returns'           => 'ReturnController',
        'transfer_orders'   => 'TransferOrderController',
    ]);
});