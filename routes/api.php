<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\BatchController;

Route::post('/stock-movements', [StockMovementController::class, 'store']);
Route::delete('/batches/{id}', [BatchController::class, 'destroy']);
