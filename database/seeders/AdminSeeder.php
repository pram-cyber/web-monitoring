<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@smartwaste.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'User Demo',
            'email'    => 'user@smartwaste.com',
            'password' => Hash::make('user123'),
            'role'     => 'user',
        ]);
    }
}