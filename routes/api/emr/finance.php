<?php 
use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'finance'], function () {
    Route::get('/deposits/initials',                    'DepositController@initials')->name('deposits.initials');
    Route::put('/price_lists/{id}/search',              'PriceListController@search')->name('price_lists.search');
    Route::get('/transactions/patients/{id}/pending',   'TransactionController@patient_pending')->name('transactions.patient_pending');
    Route::get('/transactions/patients/{id}/all',       'TransactionController@patient_transactions')->name('transactions.patient_transactions');
    Route::apiResources([
        '/dashboard'    => 'DashboardController',
        '/deposits'     => 'DepositController',
        '/invoices'     => 'InvoiceController',
        '/payments'     => 'PaymentController',
        '/price_lists'  => 'PriceListController',
        '/transactions' => 'TransactionController',
    ]);
});