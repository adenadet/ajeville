<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'escrows'], function () {
    Route::post('/payments/filter',             'PaymentController@filter')->name('payments.filter');
    Route::post('/payments/generate_report',    'PaymentController@generateReport')->name('payments.generate_report');
    Route::post('/products/generate_report',    'ProductController@generate_report')->name('payments.generate_report');
    Route::put( '/transactions/accept/{id}',    'TransactionController@accept')->name('transactions.accept');
    Route::put( '/transactions/{id}/cancel',    'TransactionController@cancel')->name('transactions.cancel');
    Route::put( '/transactions/complete/{id}',  'TransactionController@complete')->name('transactions.complete');
    Route::put( '/transactions/confirm/{id}',   'TransactionController@confirm')->name('transactions.confirm');
    Route::get( '/transactions/initials',       'TransactionController@initials')->name('transactions.initials');
    Route::put( '/transactions/payment/{id}',   'TransactionController@payment')->name('transactions.payment');
    Route::post('/transactions/generate_report','TransactionController@generateReport')->name('transactions.generate_report');
    
    Route::apiResources([
        '/dashboard'            => 'DashboardController',
        '/disputes'             => 'DisputeController',
        '/partners'             => 'PartnerController',
        '/payments'             => 'PaymentController',
        '/products'             => 'ProductController',
        '/quick_transactions'   => 'QuickTransactionController',
        '/transactions'         => 'TransactionController',
        '/vendors'              => 'VendorController',
    ]);
});