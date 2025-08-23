<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_stock_movement(): void
    {
        $product = Product::factory()->create();
        $location = Location::factory()->create();
        $batch = Batch::factory()->create(['product_id' => $product->id, 'location_id' => $location->id]);

        $payload = [
            'product_id' => $product->id,
            'quantity' => 3,
            'location_id' => $location->id,
            'batch_id' => $batch->id,
            'notes' => 'test',
        ];

        $res = $this->postJson('/api/stock-movements', $payload);
        $res->assertCreated();
        $res->assertJsonFragment(['product_id' => $product->id, 'quantity' => 3]);
    }

    public function test_delete_batch_endpoint(): void
    {
        $product = Product::factory()->create();
        $location = Location::factory()->create();
        $batch = Batch::factory()->create(['product_id' => $product->id, 'location_id' => $location->id]);

        $res = $this->deleteJson('/api/batches/'.$batch->id);
        $res->assertOk();

        $res404 = $this->deleteJson('/api/batches/99999');
        $res404->assertStatus(404);
    }
}
