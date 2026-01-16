<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@smartvolunteer.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@12345'),
                'role' => 'admin',
            ]
        );
    }
}
