<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ums/registration_complete/{id}',  'App\Http\Controllers\Api\Ums\RegistrationController@show')->name('api.dashboard.applicant');

Route::namespace('App\Http\Controllers\Api\Approvals')->name('api.approvals.')->group(base_path('routes/api/approvals.php'));
Route::namespace('App\Http\Controllers\Api\Archive')->name('api.archives.')->group(base_path('routes/api/archives.php'));
Route::namespace('App\Http\Controllers\Api\Auth')->name('api.auth.')->group(base_path('routes/api/auth.php'));
Route::namespace('App\Http\Controllers\Api\Chats')->middleware('auth:api')->name('api.chats.')->group(base_path('routes/api/chats.php'));
Route::namespace('App\Http\Controllers\Api\CRM')->middleware('auth:api')->name('api.crm.')->group(base_path('routes/api/crm.php'));
Route::namespace('App\Http\Controllers\Api\EMR')->middleware('auth:api')->name('api.emr.')->group(base_path('routes/api/emr.php'));
Route::namespace('App\Http\Controllers\Api\Equipments')->middleware('auth:api')->name('api.equipments.')->group(base_path('routes/api/equipments.php'));
Route::namespace('App\Http\Controllers\Api\Escrows')->middleware('auth:api')->name('api.escrows.')->group(base_path('routes/api/escrows.php'));
Route::namespace('App\Http\Controllers\Api\Finance')->middleware('auth:api')->name('api.finance.')->group(base_path('routes/api/finance.php'));
Route::namespace('App\Http\Controllers\Api\Icms')->middleware('auth:api')->name('api.icms.')->group(base_path('routes/api/icms.php'));
Route::namespace('App\Http\Controllers\Api\Hrms')->middleware('auth:api')->name('api.hrms.')->group(base_path('routes/api/hrms.php'));
Route::namespace('App\Http\Controllers\Api\Inventory')->middleware('auth:api')->name('api.inventory.')->group(base_path('routes/api/inventory.php'));
Route::namespace('App\Http\Controllers\Api\Learn')->middleware('auth:api')->name('api.learn.')->group(base_path('routes/api/learn.php'));
Route::namespace('App\Http\Controllers\Api\Operations')->middleware('auth:api')->name('api.operations.')->group(base_path('routes/api/operations.php'));
Route::namespace('App\Http\Controllers\Api\Procurement')->middleware('auth:api')->name('api.procurement.')->group(base_path('routes/api/procurement.php'));
Route::namespace('App\Http\Controllers\Api\Sales')->middleware('auth:api')->name('api.sales.')->group(base_path('routes/api/sales.php'));
Route::namespace('App\Http\Controllers\Api\Som')->name('api.som.')->group(base_path('routes/api/som.php'));
Route::namespace('App\Http\Controllers\Api\Ticketing')->middleware('auth:api')->name('api.tickets.')->group(base_path('routes/api/ticket.php'));
Route::namespace('App\Http\Controllers\Api\ToDo')->name('api.todos.')->group(base_path('routes/api/todo.php'));
Route::namespace('App\Http\Controllers\Api\Ums')->middleware('auth:api')->name('api.ums.')->group(base_path('routes/api/ums.php'));

Route::get('/dashboard/applicant',  'App\Http\Controllers\Api\DashboardController@applicant')->name('api.dashboard.applicant');
//Route::get('/schedules', 'App\Http\Controllers\Api\EMR\RegistrationController@schedules')->name('appointments.schedules');
Route::post('/notices/modify',    'App\Http\Controllers\Api\NoticeController@modify')->name('api.notices.modify');
Route::get('/policies/all/{id}',  'App\Http\Controllers\Api\PolicyController@all')->name('api.policies.all');
Route::post('/policies/assign',   'App\Http\Controllers\Api\PolicyController@assign')->name('api.policies.assign');

//Route::get('/schedulers/cancel', 'App\Http\Controllers\Api\EMR\RegistrationController@cancel')->name('schedulers.cancel');
//Route::post('/schedulers/cancelled', 'App\Http\Controllers\Api\EMR\RegistrationController@cancelled')->name('schedulers.cancelled');
Route::get('/registration/initials', 'App\Http\Controllers\Api\Ums\RegistrationController@initials')->name('registration.initials');
Route::apiResources([
    //'certificates'  => 'App\Http\Controllers\Api\EMR\CertificateController',
    //'emr/cancellations' => 'App\Http\Controllers\Api\EMR\CancellationController',
    'dashboard'                 => 'App\Http\Controllers\Api\DashboardController',
    'member'                    => 'App\Http\Controllers\Api\MemberController',
    'notices'                   => 'App\Http\Controllers\Api\NoticeController',
    'policies'                  => 'App\Http\Controllers\Api\PolicyController',
    'registration'              => 'App\Http\Controllers\Api\Ums\RegistrationController',
    //'scheduler'     => 'App\Http\Controllers\Api\EMR\RegistrationController',
]);
