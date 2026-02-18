<?php 
use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'emr/finance', 'as'=>'api.emr.finance.'], function () {
    Route::get('/deposits/initials',                    'DepositController@initials')->name('deposits.initials');
    Route::get('/transactions/patients/{id}/pending',   'TransactionController@patient_pending')->name('transactions.patient_pending');
    Route::get('/transactions/patients/{id}/all',       'TransactionController@patient_transactions')->name('transactions.patient_transactions');
    Route::get('/transactions/{id}/payment',            'TransactionController@payment')->name('transactions.payment');
    
    Route::apiResources([
        '/dashboard'    => 'DashboardController',
        '/deposits'     => 'DepositController',
        '/payments'     => 'PaymentController',
        '/transactions' => 'TransactionController',
    ]);
});