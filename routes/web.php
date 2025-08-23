<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\UserRoleController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\BatchController;

// Routes publiques (pas d'auth): login (on retire explicitement Authenticate pour éviter la boucle)
Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login')
        ->withoutMiddleware([\Illuminate\Auth\Middleware\Authenticate::class]);
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post')
        ->withoutMiddleware([\Illuminate\Auth\Middleware\Authenticate::class]);
});

// Routes authentifiées par défaut
Route::middleware(['web','auth'])->group(function () {
    Route::get('/', function () { return view('home'); });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Listes visibles à tous les connectés
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');

    // Gestion et CRUD réservés admin/dev
    Route::middleware(['ensure.role:admin'])->group(function () {
        // Utilisateurs
        Route::get('/users', [UserRoleController::class, 'index'])->name('users.index');
        Route::post('/users', [UserRoleController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserRoleController::class, 'update'])->name('users.update');

        // Produits (création/édition/suppression)
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Lieux (création/édition/suppression)
        Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        Route::get('/locations/{id}/edit', [LocationController::class, 'edit'])->name('locations.edit');
        Route::put('/locations/{id}', [LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{id}', [LocationController::class, 'destroy'])->name('locations.destroy');

        // Lots (création/édition/suppression)
        Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create');
        Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
        Route::get('/batches/{id}/edit', [BatchController::class, 'edit'])->name('batches.edit');
        Route::put('/batches/{id}', [BatchController::class, 'update'])->name('batches.update');
        Route::delete('/batches/{id}', [BatchController::class, 'destroy'])->name('batches.destroy');
    });
});
