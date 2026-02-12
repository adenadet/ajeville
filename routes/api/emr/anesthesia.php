<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/anesthesia', 'as'=>'api.emr.anesthesia.'], function () {
    
    Route::apiResources([
        '/cases'            => 'CaseController',
        '/dashboard'        => 'DashboardController',
        '/drug_admin'       => 'DrugController',
        '/pre_ops'          => 'PreOpController',
        '/post_ops'         => 'PostOpController',
        '/vital_signs'      => 'VitalController',
    ]);
});