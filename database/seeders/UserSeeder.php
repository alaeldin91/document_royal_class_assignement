<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Assuming you already have roles created
        $adminRole = Role::where('name', 'admin')->first();  // Get admin role
        $userRole = Role::where('name', 'user')->first();    // Get user role

        // Create a user and assign the role_id
        User::create([
            'user_name' => 'Musa Suliman',
            'email' => 'musa.suliman@example.com',
            'password' => Hash::make('password123'),
            'role_id' => $adminRole->id, // Assign role_id here
        ]);

        // You can add more users with roles as needed
    }
}

