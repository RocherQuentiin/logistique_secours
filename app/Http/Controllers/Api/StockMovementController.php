<?php

namespace App\Http\Controllers\Api;

use App\Actions\CreateStockMovementAction;
use App\Http\Requests\StoreStockMovementRequest;
use Illuminate\Http\JsonResponse;

class StockMovementController
{
    public function store(StoreStockMovementRequest $request, CreateStockMovementAction $action): JsonResponse
    {
        $movement = $action->execute($request->validated());
        return response()->json($movement, 201);
    }
}
