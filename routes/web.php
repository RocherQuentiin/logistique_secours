<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserRoleController;
use App\Http\Controllers\Web\AuthController;

Route::get('/', function () {
    return view('home');
});

// Auth web (session)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Gestion des rôles (protégée: connecté + rôle dev)
Route::middleware(['web','auth','ensure.role:dev'])->group(function () {
    Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
    Route::put('/users/{id}', [UserRoleController::class, 'update'])->name('users.update');
});
