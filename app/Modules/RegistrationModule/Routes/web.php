<?php

use App\Modules\RegistrationModule\Http\Controllers\RegistrationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:peserta'])->group(function () {
    Route::get('/my-tickets', [RegistrationController::class, 'index'])->name('registrations.index');
    Route::post('/registrations', [RegistrationController::class, 'store'])->name('registrations.store');
});
