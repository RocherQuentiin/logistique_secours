<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create default roles with predictable emails and password 'password'
        $defaults = [
            ['name' => 'Dev AVSS78', 'email' => 'dev@avss78.local', 'role' => 'dev'],
            ['name' => 'Admin AVSS78', 'email' => 'admin@avss78.local', 'role' => 'admin'],
            ['name' => 'User AVSS78', 'email' => 'user@avss78.local', 'role' => 'user'],
        ];

        foreach ($defaults as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => 'password', // hashed by model cast
                    'role' => $data['role'],
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
