<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role; // Import the Role model from Spatie package

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'teacher', 'guard_name' => 'web']);
        Role::create(['name' => 'student', 'guard_name' => 'web']);
        // Add more roles as needed
    }
}
