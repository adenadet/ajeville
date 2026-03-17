<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'emr/hims', 'as'=>'api.emr.hims.'], function () {
    Route::post('/appointments/available_slots',     'AppointmentController@available_slots')->name('appointments.available_slots');
    Route::post('/appointments/check_in',            'AppointmentController@check_in')->name('appointments.check_in');
    Route::get( '/appointments/initials',            'AppointmentController@initials')->name('appointments.initials');
    Route::get( '/consultations/begins',             'ConsultationController@begins')->name('consultations.begins');
    Route::get( '/consultations/initials',           'ConsultationController@initials')->name('consultations.initials');
    Route::get( '/consultations/initials/{id}',      'ConsultationController@visit_initials')->name('consultations.visit_initials');
    Route::get( '/consultations/visit/{id}',         'ConsultationController@visit')->name('consultations.visit');
    Route::get( '/drugs/initials',                   'DrugController@initials')->name('drugs.initials');
    Route::get( '/drugs/search',                     'DrugController@search')->name('drugs.search');
    Route::get( '/insurances/initials',              'InsuranceController@initials')->name('insurances.initials');
    Route::get( '/investigations/initials/{id}',     'InvestigationController@initials')->name('investigations.initials');
    Route::get( '/laboratory/initials',              'LaboratoryController@initials')->name('laboratory.initials');
    Route::get( '/patients/all',                     'PatientController@all')->name('patients.all');
    Route::get( '/patients/initials',                'PatientController@initials')->name('patients.initials');
    Route::get( '/patients/{id}/insurances',         'PatientController@insurances')->name('patients.insurances');
    Route::post('/patients/merge',                   'PatientController@merge')->name('patients.merge');
    Route::get( '/patients/merge_preview',           'PatientController@merge_preview')->name('patients.merge_preview');
    Route::get( '/patients/search',                  'PatientController@search')->name('patients.search');
    Route::post('/patients/set_cookie',              'PatientController@set_cookie')->name('patients.set_cookie');
    Route::get( '/prescriptions/initials',           'PrescriptionController@initials')->name('prescriptions.initials');
    Route::get( '/queues/doctor',                    'QueueController@doctor')->name('queues.doctor');
    Route::get( '/queues/dialysis',                  'QueueController@dialysis')->name('queues.dialysis');
    Route::get( '/queues/emergency',                 'QueueController@emergency')->name('queues.emergency');
    Route::get( '/queues/laboratory',                'QueueController@laboratory')->name('queues.laboratory');
    Route::get( '/queues/physio',                    'QueueController@physio')->name('queues.physio');
    Route::get( '/queues/radiology',                 'QueueController@radiology')->name('queues.radiology');
    Route::get( '/queues/vitals',                    'QueueController@vitals')->name('queues.vitals');
    Route::get( '/radiology/initials',               'RadiologyController@initials')->name('radiology.initials');
    Route::get( '/services/initials',                'ServiceController@initials')->name('services.initials');
    Route::get( '/visits/bills/{id}',                'VisitationController@bills')->name('visits.bills');
    Route::put( '/visits/{id}/end',                  'VisitationController@end')->name('visits.end');
    Route::get( '/visits/{id}/end-check',            'VisitationController@end_check')->name('visits.end-check');
    Route::get( '/visits/get_cookie',                'VisitationController@get_cookie')->name('visits.get_cookie');
    Route::get( '/visits/{id}/initials',             'VisitationController@initials')->name('visits.initials');
    Route::get( '/visits/{id}/start',                'VisitationController@start')->name('visits.start');
    Route::post('/visits/set_cookie',                'VisitationController@set_cookie')->name('visits.set_cookie');
    Route::post('/visits/transactions',              'VisitationController@transactions')->name('visits.transactions');
    Route::get( '/visit_transactions/{id}/pending',  'VisitTransactionController@pending')->name('visit_transactions.pending');
    
    Route::apiResources([
        'allergies'             => 'AllergyController',
        'allergy_types'         => 'AllergyTypeController',
        'appointments'          => 'AppointmentController',
        'consultations'         => 'ConsultationController',
        'contacts'              => 'ContactController',
        'dashboard'             => 'DashboardController',
        'drugs'                 => 'DrugController',
        'laboratory'            => 'LaboratoryController',
        'insurances'            => 'InsuranceController',
        'investigations'        => 'InvestigationController',
        'patients'              => 'PatientController',
        'prescriptions'         => 'PrescriptionController',
        'queues'                => 'QueueController',
        'radiology'             => 'RadiologyController',
        'services'              => 'ServiceController',
        'service_types'         => 'ServiceTypeController',
        'visits'                => 'VisitationController',
        'visit_transactions'    => 'VisitTransactionController',
        'vitals'                => 'VitalController',
    ]);
});
