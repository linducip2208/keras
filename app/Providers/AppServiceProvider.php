<?php

namespace App\Providers;

use App\Services\LicenseClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LicenseClient::class);
    }

    public function boot(): void
    {
        //
    }
}
