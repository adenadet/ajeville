<?php

use Illuminate\Support\Facades\Route;

Route::namespace('Admission')->middleware('auth:api')->group(base_path('routes/api/emr/admission.php'));
Route::namespace('Anesthesia')->middleware('auth:api')->group(base_path('routes/api/emr/anesthesia.php'));
Route::namespace('Assessment')->middleware('auth:api')->group(base_path('routes/api/emr/assessment.php'));
Route::namespace('Consultation')->middleware('auth:api')->group(base_path('routes/api/emr/consultation.php'));
Route::namespace('Domiciliary')->middleware('auth:api')->group(base_path('routes/api/emr/domiciliary.php'));
Route::namespace('Hims')->middleware('auth:api')->group(base_path('routes/api/emr/hims.php'));
Route::namespace('Insurance')->middleware('auth:api')->group(base_path('routes/api/emr/insurance.php'));
Route::namespace('Laboratory')->middleware('auth:api')->group(base_path('routes/api/emr/laboratory.php'));
Route::namespace('Nursing')->middleware('auth:api')->group(base_path('routes/api/emr/nursing.php'));
Route::namespace('Pharmacy')->middleware('auth:api')->group(base_path('routes/api/emr/pharmacy.php'));
Route::namespace('Radiology')->middleware('auth:api')->group(base_path('routes/api/emr/radiology.php'));