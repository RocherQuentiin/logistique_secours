<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum','ensure.role:user'])->group(function () {
	Route::post('/stock-movements', [StockMovementController::class, 'store']);
});

Route::middleware(['auth:sanctum','ensure.role:admin'])->group(function () {
	Route::delete('/batches/{id}', [BatchController::class, 'destroy']);
});

Route::middleware(['auth:sanctum','ensure.role:admin'])->group(function () {
	Route::get('/users', [UserController::class, 'index']);
	Route::put('/users/{id}/role', [UserController::class, 'updateRole']);
});
