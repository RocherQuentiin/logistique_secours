<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiStockTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(string $role = 'user'): string
    {
        $user = User::factory()->create(['role' => $role, 'password' => bcrypt('password')]);
        $res = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'password']);
        $res->assertOk();
        return $res->json('token');
    }

    public function test_can_create_stock_movement(): void
    {
    $token = $this->tokenFor('user');
    $this->withHeader('Authorization', 'Bearer '.$token);
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
    $token = $this->tokenFor('admin');
    $this->withHeader('Authorization', 'Bearer '.$token);
        $product = Product::factory()->create();
        $location = Location::factory()->create();
        $batch = Batch::factory()->create(['product_id' => $product->id, 'location_id' => $location->id]);

        $res = $this->deleteJson('/api/batches/'.$batch->id);
        $res->assertOk();

        $res404 = $this->deleteJson('/api/batches/99999');
        $res404->assertStatus(404);
    }
}
