<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function tokenFor(string $role): string
    {
    $user = User::factory()->create(['role' => $role]);
    $res = $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'password']);
    $res->assertOk();
    return $res->json('token');
    }

    public function test_user_can_post_stock_movement_but_cannot_delete_batch(): void
    {
        $token = $this->tokenFor('user');
        $this->withHeader('Authorization', 'Bearer '.$token);
        $this->postJson('/api/stock-movements', [])->assertStatus(422); // validation error acceptable
        $this->deleteJson('/api/batches/1')->assertStatus(403);
    }

    public function test_admin_can_delete_batch(): void
    {
        $token = $this->tokenFor('admin');
        $this->withHeader('Authorization', 'Bearer '.$token);
        $this->deleteJson('/api/batches/1')->assertStatus(404); // batch not found but authorized
    }
}
