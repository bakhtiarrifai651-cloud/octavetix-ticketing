<?php

use App\Modules\EventModule\Providers\EventServiceProvider;
use App\Modules\RegistrationModule\Providers\RegistrationServiceProvider;
use App\Modules\TicketModule\Providers\TicketServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    TicketServiceProvider::class,
    RegistrationServiceProvider::class,
];
