<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/insurance', 'as'=>'api.emr.insurance.'], function () {
    Route::get('/contacts/provider/{id}',             'ContactPersonController@provider')->name('contacts.provider');
    Route::get('/plans/provider/{id}',                'PlanController@provider')->name('plans.provider');
    Route::get('/plan_branches/initials',             'PlanBranchController@initials')->name('plan_branches.initials');

    Route::apiResources([
        'contacts'      => 'ContactPersonController',
        'auth_codes'    => 'AuthCodeController',
        'claims'        => 'ClaimsController',
        'dashboard'     => 'DashboardController',
        'plans'         => 'PlanController',
        'plan_branches' => 'PlanBranchController',
        'providers'     => 'ProviderController',
        'transactions'  => 'TransactionController',
    ]);
});
