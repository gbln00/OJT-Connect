<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TenantAdminSeeder extends Seeder
{
    public function run(
        string $name,
        string $email,
        string $password
    ): void {
        User::create([
            'name'      => $name,
            'email'     => $email,
            'password'  => Hash::make($password),
            'role'      => 'admin',
            'is_active' => true,
        ]);
    }
}