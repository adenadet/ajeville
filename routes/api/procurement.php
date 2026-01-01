<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'procurement'], function () {
    Route::put( '/batches/confirm/{id}',            'BatchController@confirm')->name('batches.confirm');
    Route::post('/purchase_orders/assign_store',    'PurchaseOrderController@assign_store')->name('purchase_orders.assign_store');        
    Route::post('/purchase_orders/assign_vendor',   'PurchaseOrderController@assign_vendor')->name('purchase_orders.assign_vendor');        
    Route::put( '/purchase_orders/approve/{id}',    'PurchaseOrderController@approve')->name('purchase_orders.approve');  
    Route::get( '/purchase_orders/initials',        'PurchaseOrderController@initials')->name('purchase_orders.initials');  
    Route::get( '/purchase_orders/initiate',        'PurchaseOrderController@initiate')->name('purchase_orders.initiate');  
    Route::get( '/purchase_orders/submit/{id}',     'PurchaseOrderController@submit')->name('purchase_orders.submit');    
    Route::put( '/purchase_orders/update/{id}',     'PurchaseOrderController@additional_costs')->name('purchase_orders.additional_costs');    
    Route::get( '/purchase_order_items/initials',   'PurchaseOrderItemController@initials')->name('purchase_order_items.initials');    
    Route::get( '/vendors/initials',                'VendorController@initials')->name('vendors.initials');
    Route::get( '/vendor_contacts/initials',        'VendorContactController@initials')->name('vendor_contacts.initials');
    Route::get( '/vendor_contacts/vendor/{id}',     'VendorContactController@vendor')->name('vendor_contacts.vendor');
    Route::post('/work_orders/assign_vendor',       'WorkOrderController@assign_vendor')->name('work_orders.assign_vendor');   
    Route::get( '/work_orders/initials',            'WorkOrderController@initials')->name('work_orders.initials');
    
    Route::apiResources([
        'dashboard'                 => 'DashboardController',
        'batches'                   => 'BatchController',
        'payment_terms'             => 'PaymentTermController',
        'purchase_orders'           => 'PurchaseOrderController',
        'purchase_order_items'      => 'PurchaseOrderItemController',
        'vendors'                   => 'VendorController',
        'vendor_contacts'           => 'VendorContactController',
        'vendor_categories'         => 'VendorCategoryController',
        'work_orders'               => 'WorkOrderController',
        'work_order_items'          => 'WorkOrderItemController',
    ]);
});