<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserRoleController;
use App\Http\Controllers\Web\AuthController;

// Routes publiques (pas d'auth): login
Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Routes authentifiées par défaut
Route::middleware(['web','auth'])->group(function () {
    Route::get('/', function () { return view('home'); });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Gestion des rôles (admin/dev)
    Route::middleware(['ensure.role:admin'])->group(function () {
        Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
        Route::post('/users', [UserRoleController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserRoleController::class, 'update'])->name('users.update');
    });
});
