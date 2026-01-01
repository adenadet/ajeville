<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'sales'], function () {
    Route::get( '/customers/initials',        'CustomerController@initials')->name('customers.initials');
    Route::get( '/customer_contacts/initials','CustomerContactController@initials')->name('customer_contacts.initials');
    Route::get( '/customer_contacts/customer/{id}', 'CustomerContactController@customer')->name('customer_contacts.customer');
    
    Route::post('/orders/assign_customer',  'OrderController@assign_customer')->name('orders.assign_customer');        
    Route::put( '/orders/approve/{id}',     'OrderController@approve')->name('orders.approve');
    Route::get( '/orders/{id}/complete',    'OrderController@complete')->name('orders.complete');
    Route::get( '/orders/display',          'OrderController@display')->name('orders.display');
    Route::get( '/orders/initials',         'OrderController@initials')->name('orders.initials');  
    Route::get( '/orders/submit/{id}',      'OrderController@submit')->name('orders.submit');    
    Route::put( '/orders/update/{id}',      'OrderController@additional_costs')->name('orders.additional_costs');    
    Route::get( '/order_items/initials',    'OrderItemController@initials')->name('order_items.initials');    
    Route::get( '/quotations/confirm/{id}', 'QuotationController@confirm')->name('quotations.confirm'); 
    Route::get( '/quotations/initials',     'QuotationController@initials')->name('quotations.initials');
    Route::get( '/quotations/mail/{id}',    'QuotationController@mail')->name('quotations.mail');    
    Route::get( '/returns/initials',        'ReturnController@initials')->name('returns.initials');
      
    Route::apiResources([
        'customers'                     => 'CustomerController',
        'customer_contacts'             => 'CustomerContactController',
        'customer_categories'           => 'CustomerCategoryController',
        'dashboard'                     => 'DashboardController',
        'delivery_notes'                => 'GoodsDeliveredController',
        'orders'                        => 'OrderController',
        'order_items'                   => 'OrderItemController',
        'quotations'                    => 'QuotationController',
        'reports'                       => 'ReportController',
        'returns'                       => 'ReturnController',
    ]);
});