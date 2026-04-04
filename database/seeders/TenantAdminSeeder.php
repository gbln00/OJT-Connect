<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class TenantAdminSeeder extends Seeder
{
    public function __construct(
        private string $name     = 'Tenant Admin',
        private string $email    = 'tenantadmin@ojtconnect.edu',
        private string $password = 'password',
    ) {}

    public function run(): void
    {
        // User::firstOrCreate(
        //     ['email' => $this->email],
        //     [
        //         'name'      => $this->name,
        //         'password'  => Hash::make($this->password),
        //         'role'      => 'admin',
        //         'is_active' => true,
        //     ]
        // );
    }
}