<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'app_url' => 'https://ajeville.test/',
    'author' => 'Squarem Information Communications',
    'name' => 'AjeVille Entreprise Solutions',
    'name_short' => 'AjeVille',
    'short_code' => 'AVL',
    'logo' => 'img/logos/logo-squarem.png',
    'logo_w' => 'img/background/logo/snh-square.png',
    'logo_h' => 'img/background/logo/snh-square.png',
    'logo_s' => 'img/background/logo/snh-square.png',
    'logo_h_w' => 'img/background/logo/snh-square.png',
    'logo_s_w' => 'img/background/logo/snh-square.png',
    'website' => 'https://squarem.com.ng',
    'email' => 'itsupport@squarem.com.ng',
    'phone' => '07012345678',
    
    
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    
    
    
    'url' => env('APP_URL', 'http://localhost:8000'),
    'asset_url' => env('ASSET_URL', null),
    'timezone' => 'Africa/Lagos',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    
    'first_central_url' => "https://online.firstcentralcreditbureau.com/firstcentralrestv2",
    'first_central_user' => "GLAZE-CREDITAPI",
    'first_central_password' => "glazecredit@100&",
    'first_central_url_test' => "https://uat.firstcentralcreditbureau.com/firstcentralrestv2",
    'first_central_user_test' => "demo",
    'first_central_password_test' => "demo@123",

    'gemini_url' => 'https://glazecredit.geminiapp.net/service/Request.svc/api',
    'gemini_strain' => 'yP5lnti7wDChJxwmBYz7dpbgbzGqQRG1dyvAZihesrA=',

    'periculum_url' => 'https://api.insights-periculum.io',
    'periculum_user' => 'insights-glazecredit-api',
    'periculum_client_strain' => 'anewSQqfl9X1eMda4OUVGUHeqbm7bQ2X',
    
    'maintenance' => [
        'driver' => 'file',
        // 'store' => 'redis',
    ],
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

        Intervention\Image\ImageServiceProvider::class,
        //Kodegrenade\NaijaFaker\OtpServiceProvider::class,
        SimpleSoftwareIO\QrCode\QrCodeServiceProvider::class,
    ])->toArray(),

    'aliases' => Facade::defaultAliases()->merge([
        // 'Example' => App\Facades\Example::class,
        'Image' => Intervention\Image\Facades\Image::class,
        // 'NaijaFaker' => Kodegrenade\NaijaFaker\NaijaFaker::class,
        'QrCode' => SimpleSoftwareIO\QrCode\Facades\QrCode::class,
    ])->toArray(),

];
