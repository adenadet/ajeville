<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/clear-cache', function() {
    //$exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    //$exitCode = Artisan::call('route:cache');
    $exitCode = Artisan::call('cache:clear');
    
    return "All done boss, anything else";
});

Route::get('/registration_complete/{id}', 'App\Http\Controllers\UserController@registration_complete')->name('registration_complete');

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth','role:Staff'],], function () {    
    Route::get('/',                     'ModulesController@dashboard')->name('home');
    Route::get('/approvals',            'ModulesController@approvals')->name('approvals');
    Route::get('/archives',             'ModulesController@archives')->name('archives');
    Route::get('/chats',                'ModulesController@chats')->name('chats');
    Route::get('/contacts',             'ModulesController@contacts')->name('contacts');
    Route::get('/coop',                 'ModulesController@coop')->name('coop');
    Route::get('/customer_relations',   'ModulesController@customer_relations')->name('customer_relations');
    Route::get('/dashboard',            'ModulesController@dashboard')->name('dashboard');
    Route::get('/departments',          'ModulesController@departments')->name('departments');
    Route::get('/equipments',           'ModulesController@equipments')->name('equipments');
    Route::get('/escrows',              'ModulesController@escrows')->name('escrows');
    Route::get('/escrow_admin',         'ModulesController@escrow_admin')->name('escrow_admin');
    Route::get('/facility',             'ModulesController@facility')->name('facility');
    Route::get('/finance',              'ModulesController@finance')->name('finance');
    Route::get('/front_office',         'ModulesController@front_office')->name('front_office');
    Route::get('/hrms',                 'ModulesController@hrms')->name('hrms');
    Route::get('/hrms_admin',           'ModulesController@hrms_admin')->name('hrms.admin');  
    Route::get('/insurance',            'ModulesController@insurance')->name('insurance');
    Route::get('/internet',             'ModulesController@internet')->name('internet');
    Route::get('/inventory',            'ModulesController@inventory')->name('inventory');
    Route::get('/loans',                'ModulesController@loans')->name('loans');
    Route::get('/loans_staff',          'ModulesController@loans_staff')->name('loans_staff');
    Route::get('/notices',              'ModulesController@notices')->name('notices');
    Route::get('/operations',           'ModulesController@operations')->name('operations');
    Route::get('/policies',             'ModulesController@policies')->name('policies');
    Route::get('/procurement',          'ModulesController@procurement')->name('procurement');
    Route::get('/profile',              'ModulesController@profile')->name('profile');
    Route::get('/sales_orders',         'ModulesController@sales_orders')->name('sales_orders');
    Route::get('/settings',             'ModulesController@settings')->name('settings');
    Route::get('/staff_month',          'ModulesController@staff_month')->name('staff_month');
    Route::get('/ticketing',            'ModulesController@ticketing')->name('ticketing');
    Route::get('/users',                'ModulesController@users')->name('users');
    
    //Auto Redirect
    Route::get('/approvals/{any}',          'ModulesController@approvals')->where('any', '.*');
    Route::get('/archives/{any}',           'ModulesController@archives')->where('any', '.*');
    Route::get('/chats/{any}',              'ModulesController@chats')->where('any', '.*');
    Route::get('/contacts/{any}',           'ModulesController@contacts')->where('any', '.*');
    Route::get('/coop/{any}',               'ModulesController@coop')->where('any', '.*');
    Route::get('/customer_relations/{any}', 'ModulesController@customer_relations')->where('any', '.*');
    Route::get('/departments/{any}',        'ModulesController@departments')->where('any', '.*');
    Route::get('/equipments/{any}',         'ModulesController@equipments')->where('any', '.*');
    Route::get('/escrows/{any}',            'ModulesController@escrows')->where('any', '.*');
    Route::get('/escrow_admin/{any}',       'ModulesController@escrow_admin')->where('any', '.*');
    Route::get('/facility/{any}',           'ModulesController@facility')->where('any', '.*');
    Route::get('/finance/{any}',            'ModulesController@finance')->where('any', '.*');
    Route::get('/front_office/{any}',       'ModulesController@front_office')->where('any', '.*');
    Route::get('/hrms/{any}',               'ModulesController@hrms')->where('any', '.*');
    Route::get('/hrms_admin/{any}',         'ModulesController@hrms_admin')->where('any', '.*'); 
    Route::get('/insurance/{any}',          'ModulesController@insurance')->where('any', '.*');
    Route::get('/internet/{any}',           'ModulesController@internet')->where('any', '.*');
    Route::get('/inventory/{any}',          'ModulesController@inventory')->where('any', '.*');
    Route::get('/loans/{any}',              'ModulesController@loans')->where('any', '.*');
    Route::get('/loans_staff/{any}',        'ModulesController@loans_staff')->where('any', '.*');
    Route::get('/notices/{any}',            'ModulesController@notices')->where('any', '.*');
    Route::get('/operations/{any}',         'ModulesController@operations')->where('any', '.*');
    Route::get('/policies/view/{id}',       'ModulesController@policy_reader'); 
    Route::get('/policies/{any}',           'ModulesController@policies')->where('any', '.*');
    Route::get('/procurement/{any}',        'ModulesController@procurement')->where('any', '.*');
    Route::get('/sales_orders/{any}',       'ModulesController@sales_orders')->where('any', '.*');
    Route::get('/settings/{any}',           'ModulesController@settings')->where('any', '.*');
    Route::get('/staff_month/{any}',        'ModulesController@staff_month')->where('any', '.*');
    Route::get('/ticketing/{any}',          'ModulesController@ticketing')->where('any', '.*');
    Route::get('/users/{any}',              'ModulesController@users')->where('any', '.*');
});

Route::group(['middleware' => ['auth', 'role:Staff'],'namespace' => 'App\Http\Controllers', 'name' => 'emr.', 'prefix' => '/emr'],function(){
    Route::get('/administrator',                            'ModulesController@administrator')->name('administrator');
    Route::get('/consultations',                            'ModulesController@consultation')->name('consultation');
    Route::get('/front_office',                             'ModulesController@front_office')->name('front_office');
    Route::get('/laboratory',                               'ModulesController@laboratory')->name('laboratory');
    Route::get('/insurance',                                'ModulesController@insurance')->name('insurance');
    Route::get('/nursing',                                  'ModulesController@nursing')->name('nursing_care');
    Route::get('/operations',                               'ModulesController@operations')->name('operations');
    Route::get('/physiotheraphy',                           'ModulesController@physiotheraphy')->name('physiotheraphy');
    Route::get('/radiology',                                'ModulesController@radiology')->name('radiology');
    
    Route::get('/administrator/{any}',                      'ModulesController@administrator')->where('any', '.*');
    Route::get('/consultations/{any}',                      'ModulesController@consultation')->where('any', '.*');
    Route::get('/front_office/{any}',                       'ModulesController@front_office')->where('any', '.*');
    Route::get('/insurance/{any}',                          'ModulesController@insurance')->where('any', '.*');
    Route::get('/laboratory/{any}',                         'ModulesController@laboratory')->where('any', '.*');
    Route::get('/nursing/{any}',                            'ModulesController@nursing')->where('any', '.*');
    Route::get('/operations/{any}',                         'ModulesController@operations')->where('any', '.*');
    Route::get('/physiotheraphy/{any}',                     'ModulesController@physiotheraphy')->where('any', '.*');
    Route::get('/radiology/{any}',                          'ModulesController@radiology')->where('any', '.*'); 
});

Route::group(['middleware' => ['auth', 'role:Staff'],'namespace' => 'App\Http\Controllers', 'as' => 'learn.', 'prefix' => '/learn'],function(){
    Route::get('/admin',                                    'ModulesController@learn_admin')->name('admin');
    Route::get('/student',                                  'ModulesController@learn_student')->name('student');
    Route::get('/tutor',                                    'ModulesController@learn_tutor')->name('front_office');
    
    Route::get('/admin/{any}',                              'ModulesController@learn_admin')->where('any', '.*')->name('admin.any');
    Route::get('/student/{any}',                           'ModulesController@learn_student')->where('any', '.*')->name('student.any');
    Route::get('/tutor/{any}',                              'ModulesController@learn_tutor')->where('any', '.*')->name('tutor.any');
});

Route::get('/{pathMatch}', function () {return view('app');})->where('pathMatch', '.*');


