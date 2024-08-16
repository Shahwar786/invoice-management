<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $campRole = Role::firstOrCreate(['name' => 'Camp']);

        // Create Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Create Camp user
        User::create([
            'name' => 'Camp User',
            'email' => 'camp@example.com',
            'password' => Hash::make('password'),
            'role_id' => $campRole->id,
        ]);
    }
}
