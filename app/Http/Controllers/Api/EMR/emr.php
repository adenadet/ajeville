<?php
use Illuminate\Support\Facades\Route;

Route::namespace('Assessment')->middleware('auth:api')->group(base_path('routes/api/emr/assessment.php'));
Route::namespace('Consultation')->middleware('auth:api')->group(base_path('routes/api/emr/consultation.php'));
Route::namespace('Domiciliary')->middleware('auth:api')->group(base_path('routes/api/emr/domiciliary.php'));
Route::namespace('Hims')->middleware('auth:api')->group(base_path('routes/api/emr/hims.php'));
Route::namespace('Insurance')->middleware('auth:api')->group(base_path('routes/api/emr/insurance.php'));
Route::namespace('Laboratory')->middleware('auth:api')->group(base_path('routes/api/emr/laboratory.php'));
Route::namespace('Nursing')->middleware('auth:api')->group(base_path('routes/api/emr/nursing.php'));
Route::namespace('Pharmacy')->middleware('auth:api')->group(base_path('routes/api/emr/pharmacy.php'));
Route::namespace('Radiology')->middleware('auth:api')->group(base_path('routes/api/emr/radiology.php'));

Route::group(['prefix'=>'emr', 'as'=>'api.emr.'], function () {
    Route::get('/appointments/initials', 'AppointmentController@initials')->name('appointments.initials');

    Route::apiResources([
        '/appointments' => 'AppointmentController',
        '/patients'     => 'PatientController',
        '/payments'     => 'PaymentController',
    ]);
});




