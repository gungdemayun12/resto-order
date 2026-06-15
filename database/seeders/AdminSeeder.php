<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@resto.com'],
            [
                'name' => 'Admin Resto',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'permissions' => User::defaultPermissions('owner'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@resto.com'],
            [
                'name' => 'Kasir Resto',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'permissions' => User::defaultPermissions('cashier'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'dapur@resto.com'],
            [
                'name' => 'Dapur Resto',
                'password' => Hash::make('password'),
                'role' => 'kitchen',
                'permissions' => User::defaultPermissions('kitchen'),
                'email_verified_at' => now(),
            ]
        );
    }
}
