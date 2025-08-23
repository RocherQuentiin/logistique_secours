<?php

namespace App\Actions;

use App\Models\StockMovement;
use App\Models\Stock;
use App\Models\Batch;
use App\Jobs\SendLowStockEmail;
use Illuminate\Support\Facades\DB;

class CreateStockMovementAction
{
    /**
     * Create a stock movement and update stock & batch quantities.
     *
     * @param array $data [
     *   'product_id' => int,
     *   'quantity' => int, // positive for incoming, negative for outgoing
     *   'location_id' => ?int,
     *   'batch_id' => ?int,
     *   'notes' => ?string,
     * ]
     *
     * @return StockMovement
     */
    public function execute(array $data)
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create([
                'product_id' => $data['product_id'] ?? null,
                'quantity' => $data['quantity'] ?? 0,
                'location_id' => $data['location_id'] ?? null,
                'batch_id' => $data['batch_id'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            // Update or create stock row
            $stock = Stock::firstOrCreate(
                [
                    'product_id' => $movement->product_id,
                    'location_id' => $movement->location_id,
                ],
                ['quantity' => 0]
            );

            $stock->quantity = max(0, $stock->quantity + $movement->quantity);
            $stock->save();

            // Update batch quantity if present
            if ($movement->batch_id) {
                $batch = Batch::find($movement->batch_id);
                if ($batch) {
                    $batch->quantity = max(0, $batch->quantity + $movement->quantity);
                    $batch->save();
                }
            }

            // If stock below threshold, dispatch notification job (placeholder threshold 5)
            if ($stock->quantity <= 5) {
                SendLowStockEmail::dispatch($stock->product_id, $stock->quantity);
            }

            return $movement;
        });
    }
}
