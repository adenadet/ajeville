<?php

namespace App\Providers;

use App\Models\EMR\Admission\Request as AdmissionRequest;
use App\Observers\EMR\AdmissionObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        AdmissionRequest::observe(AdmissionObserver::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
