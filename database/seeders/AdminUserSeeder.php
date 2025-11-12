<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\SensorData;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'anto32299@gmail.com')->exists()) {
            User::insert([
                [
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
                ]
            ]);
        }

        Device::insert([
            [
                'name' => 'Aerator',
                'status' => "OFF",
                'description' => 'Mengontrol suplai oksigen ke kolam.'
            ],
            [
                'name' => 'Pompa',
                'status' => "OFF",
                'description' => 'Mengontrol sirkulasi atau pengurasan air.'
            ],
            [
                'name' => 'Feeder',
                'status' => "OFF",
                'description' => 'Mengatur pemberian pakan lobster.'
            ]
        ]);
    }
}
