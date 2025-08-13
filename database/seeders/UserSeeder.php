<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@sooqnaa.com',
            'phone' => '+1234567890',
            'password_hash' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified' => true,
            'phone_verified' => true,
        ]);

        // Create sample merchant
        User::create([
            'first_name' => 'Merchant',
            'last_name' => 'User',
            'email' => 'merchant@sooqnaa.com',
            'phone' => '+1234567891',
            'password_hash' => Hash::make('password123'),
            'role' => 'merchant',
            'status' => 'active',
            'email_verified' => true,
            'phone_verified' => true,
        ]);

        // Create sample customer
        User::create([
            'first_name' => 'Customer',
            'last_name' => 'User',
            'email' => 'customer@sooqnaa.com',
            'phone' => '+1234567892',
            'password_hash' => Hash::make('password123'),
            'role' => 'customer',
            'status' => 'active',
            'email_verified' => true,
            'phone_verified' => true,
        ]);
    }
}
