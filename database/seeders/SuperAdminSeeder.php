<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@ojtconnect.com')->exists()) {
            User::create([
                'name'     => 'Super Admin',
                'email'    => 'admin@ojtconnect.com',
                'password' => bcrypt('password123'),
                'role'     => 'super_admin',
            ]);
        }
    }
}