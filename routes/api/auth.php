<?php

use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>'auth'], function () {
    Route::post('/login', 'AuthController@login')->name('login');
    Route::post('/register', 'AuthController@register')->name('register');
    Route::post('/reset/otp', 'AuthController@reset_otp')->name('reset-otp');
    Route::post('/reset/password', 'AuthController@reset')->name('reset-password');
    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/complete-registration', 'AuthController@complete_registration')->name('complete-registration');
        Route::post('/complete-registration-via-otp', 'AuthController@complete_registration_via_otp')->name('complete-registration-via-otp');
        Route::get( '/get-otp', 'AuthController@get_email_verification_otp')->name('get-otp');
        Route::post('/logout', 'AuthController@logout')->name('logout');
        Route::get( '/user', 'AuthController@user')->name('user');
    });
});