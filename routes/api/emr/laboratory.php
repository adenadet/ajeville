<?php 
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/laboratory', 'as'=>'api.emr.laboratory.'], function () {

    Route::get( '/dashboard',                    'RequestController@dashboard')->name('dashboard');
    Route::get( '/reference_ranges/initials',    'ReferenceRangeController@initials')->name('reference_ranges.initials');
    Route::get( '/requests/collect/{id}',        'RequestController@collect')->name('requests.collect');
    Route::get( '/requests/initials',            'RequestController@initials')->name('laboratory.initials');
    Route::get( '/requests/insurance',           'RequestController@insurance')->name('requests.insurance');
    Route::get( '/requests/{id}/start',          'RequestController@start')->name('requests.start'); 
    Route::get( '/requests/{id}/start_report',   'RequestController@start_report')->name('requests.start_report'); 
    Route::get( '/results/{id}/initials',        'ResultsController@initials')->name('results.initials');      
    Route::put( '/results/{id}/verify',          'ResultsController@verify')->name('results.verify');      
    Route::get( '/result_templates/initials',    'ResultTemplateController@initials')->name('result_templates.initials');      
    Route::get( '/services/initials',            'ServiceController@initials')->name('services.initials');
    Route::post('/specimens/action',             'SpecimenController@action')->name('specimens.action');
    Route::get( '/specimens/{id}/initials',      'SpecimenController@initials')->name('specimens.initials');

    Route::apiResources([
        'analytes'          => 'AnalyteController',
        'bottles'           => 'BottleController',
        'dashboard'         => 'DashboardController',
        'queues'            => 'QueueController',
        'reference_ranges'  => 'ReferenceRangeController',
        'requests'          => 'RequestController',
        'results'           => 'ResultsController',
        'result_templates'  => 'ResultTemplateController',
        'services'          => 'ServiceController',
        'specimens'         => 'SpecimenController',    
        'specimen_types'    => 'SpecimenTypeController',
    ]);
    
});