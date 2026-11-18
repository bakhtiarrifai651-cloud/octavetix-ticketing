<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    // Contoh rute yang diproteksi khusus role admin
    Route::middleware('role:admin')->get('/admin-area', function () {
        return 'Halo Admin! Proteksi RBAC berfungsi dengan baik.';
    });
});
