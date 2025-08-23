<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Location;
use App\Models\Batch;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Actions\DeleteBatchAction;

class DeleteBatchActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_archives_and_nullifies_references()
    {
        $product = Product::factory()->create();
        $location = Location::factory()->create();
        $batch = Batch::factory()->create(['product_id' => $product->id, 'location_id' => $location->id, 'quantity' => 5]);

        $stock = Stock::create(['product_id' => $product->id, 'location_id' => $location->id, 'quantity' => 5, 'batch_id' => $batch->id]);
        $movement = StockMovement::create(['product_id' => $product->id, 'quantity' => -1, 'location_id' => $location->id, 'batch_id' => $batch->id]);

        $action = new DeleteBatchAction();
        $ok = $action->execute($batch->id);

        $this->assertTrue($ok);
        $this->assertDatabaseHas('deleted_batches', ['original_batch_id' => $batch->id, 'product_id' => $product->id]);
        $this->assertDatabaseHas('stocks', ['id' => $stock->id, 'batch_id' => null]);
        $this->assertDatabaseHas('stock_movements', ['id' => $movement->id, 'batch_id' => null]);
        $this->assertDatabaseMissing('batches', ['id' => $batch->id]);
    }
}
