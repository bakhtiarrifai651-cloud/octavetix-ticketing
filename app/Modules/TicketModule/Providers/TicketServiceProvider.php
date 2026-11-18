<?php

namespace App\Modules\TicketModule\Providers;

use App\Modules\TicketModule\Contracts\TicketContract;
use App\Modules\TicketModule\Services\TicketService;
use Illuminate\Support\ServiceProvider;

class TicketServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TicketContract::class, TicketService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
    }
}
