<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        try {
            DB::table('users')->insertOrIgnore([
                'name'              => 'Super Admin',
                'email'             => 'superadmin@ojtconnect.edu',
                'password'          => Hash::make('password'),
                'role'              => 'super_admin',
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
            $this->command->info('Super admin created!');
        } catch (\Exception $e) {
            $this->command->error('Failed: ' . $e->getMessage());
        }
    }
}