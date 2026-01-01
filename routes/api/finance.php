<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'finance'], function () {
    Route::get('/branch_accounts/initials',             'BranchAccountController@initials')->name('branch_accounts.initials');
    Route::get('/branch_price_lists/initials',          'BranchPriceListController@initials')->name('price_lists.initials');
    Route::get('/deposits/initials',                    'DepositController@initials')->name('deposits.initials');
    Route::get('/expenses/initials',                    'ExpenseController@initials')->name('expenses.initials');
    Route::get('/incomes/initials',                     'IncomeController@initials')->name('incomes.initials');
    Route::put('/invoices/{id}/approve',                'InvoiceController@approve')->name('invoices.approve');
    Route::get('/invoices/{id}/expense',                'InvoiceController@expense')->name('invoices.expense');
    Route::get('/invoices/initials',                    'InvoiceController@initials')->name('invoices.initials');
    Route::get('/payments/{id}/confirm',                'PaymentController@confirm')->name('payments.confirm');
    Route::get('/payments/initials',                    'PaymentController@initials')->name('payments.initials');
    Route::get('/pay_outs/{id}/confirm',                'PayOutController@confirm')->name('payments.confirm');
    Route::get('/pay_outs/initials',                    'PayOutController@initials')->name('payments.initials');
    Route::get('/price_lists/initials',                 'PriceListController@initials')->name('price_lists.initials');
    Route::get('/price_lists/{id}/plans',               'PriceListController@plans')->name('price_lists.plans');
    Route::put('/price_lists/{id}/search',              'PriceListController@search')->name('price_lists.search');
    Route::put('/price_lists/{id}/update_items',        'PriceListController@update_items')->name('price_lists.update_items');
    Route::get('/transactions/patients/{id}/pending',   'TransactionController@patient_pending')->name('transactions.patient_pending');
    Route::get('/transactions/patients/{id}/all',       'TransactionController@patient_transactions')->name('transactions.patient_transactions');
    
    Route::apiResources([
        '/branch_accounts'      => 'BranchAccountController',
        '/branch_price_lists'   => 'BranchPricelistController',
        '/dashboard'            => 'DashboardController',
        '/deposits'             => 'DepositController',
        '/expenses'             => 'ExpenseController',
        '/expense_types'        => 'ExpenseTypeController',
        '/incomes'              => 'IncomeController',
        '/invoices'             => 'InvoiceController',
        '/payments'             => 'PaymentController',
        '/payment_modes'        => 'PaymentModeController',
        '/pay_outs'             => 'PayOutController',
        '/price_lists'          => 'PriceListController',
        '/reports'              => 'ReportController',
        '/transactions'         => 'TransactionController',
    ]);
});