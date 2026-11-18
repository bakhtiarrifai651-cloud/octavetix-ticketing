<?php

use App\Modules\RegistrationModule\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:peserta'])->group(function () {
    Route::get('/my-tickets', [RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::delete('/registrations/{id}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');
});

Route::middleware(['web', 'auth', 'role:admin'])->group(function () {
    Route::post('/events/{eventId}/check-in', [RegistrationController::class, 'checkIn'])->name('registrations.checkin');
});
