<?php

namespace App\Http\Controllers\Api;

use App\Actions\DeleteBatchAction;
use Illuminate\Http\JsonResponse;

class BatchController
{
    public function destroy(int $id, DeleteBatchAction $action): JsonResponse
    {
        $ok = $action->execute($id);
        if (! $ok) {
            return response()->json(['message' => 'Batch not found'], 404);
        }
        return response()->json(['message' => 'Batch deleted']);
    }
}
