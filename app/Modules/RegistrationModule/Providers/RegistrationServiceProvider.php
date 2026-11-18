<?php

namespace App\Modules\RegistrationModule\Providers;

use App\Modules\RegistrationModule\Contracts\RegistrationContract;
use App\Modules\RegistrationModule\Services\RegistrationService;
use Illuminate\Support\ServiceProvider;

class RegistrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RegistrationContract::class, RegistrationService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
    }
}
