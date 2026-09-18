<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Operator user (existing)
        User::create([
            'name' => 'Operator Test',
            'email' => 'operator@test.local',
            'username' => '7020001',
            'password' => Hash::make('7020001'),
            'role' => 'operator',
            'provId' => null,
            'cabangId' => null,
            'status_active' => 'active',
        ]);

        // Admin user for tests
        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.local',
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'super admin',
            'provId' => null,
            'cabangId' => null,
            'status_active' => 'active',
        ]);
    }
}
