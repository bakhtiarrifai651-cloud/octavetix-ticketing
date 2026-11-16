<?php

use App\Modules\TicketModule\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:admin'])->group(function () {
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
});