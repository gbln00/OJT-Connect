<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        try {
            User::firstOrCreate(
                ['email' => 'admin@ojtconnect.com'],
                [
                    'name'     => 'Super Admin',
                    'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'password')),
                    'role'     => 'super_admin',
                ]
            );
        } catch (\Throwable $e) {
            // Don't crash startup if DB is temporarily unreachable
            \Illuminate\Support\Facades\Log::error('SuperAdminSeeder failed: ' . $e->getMessage());
        }
    }
}