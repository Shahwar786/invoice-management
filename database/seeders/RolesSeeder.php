<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Admin', 'Camp', 'Sales Supervisor', 'Accounts', 'Staff', 'Kitchen'];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}

