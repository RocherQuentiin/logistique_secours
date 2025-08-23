<?php

namespace App\Actions;

use App\Models\Batch;
use Illuminate\Support\Facades\DB;

class DeleteBatchAction
{
    /**
     * Archive a batch into deleted_batches then remove it.
     * References in stocks and stock_movements are set to null.
     *
     * @param int $batchId
     * @return bool
     */
    public function execute(int $batchId): bool
    {
        return DB::transaction(function () use ($batchId) {
            $batch = Batch::find($batchId);
            if (! $batch) {
                return false;
            }

            // archive
            DB::table('deleted_batches')->insert([
                'original_batch_id' => $batch->id,
                'product_id' => $batch->product_id,
                'name' => $batch->name,
                'location_id' => $batch->location_id,
                'quantity' => $batch->quantity,
                'expiry_date' => $batch->expiry_date,
                'meta' => $batch->meta ? json_encode($batch->meta) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // nullify references
            DB::table('stocks')->where('batch_id', $batch->id)->update(['batch_id' => null]);
            DB::table('stock_movements')->where('batch_id', $batch->id)->update(['batch_id' => null]);

            $batch->delete();

            return true;
        });
    }
}
