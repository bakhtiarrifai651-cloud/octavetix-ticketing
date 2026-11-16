<?php

namespace App\Modules\TicketModule\Providers;

use Illuminate\Support\ServiceProvider;

class TicketServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}