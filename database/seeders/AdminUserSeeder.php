<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'anto32299@gmail.com')->exists()) {
            User::create([
                'name' => 'Anto',
                'email' => 'anto32299@gmail.com',
                'password' => Hash::make('12121212'),
                'role' => 'admin',
            ],
            [
                'name' => 'user',
                'email' => 'user@gmail.com',
                'password' => Hash::make('12121212'),
                'role' => 'peneliti',
            ]);
        }
    }
}
