<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Population;
use App\Models\SensorData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        DB::table('sensor_data')->truncate();

        $start = Carbon::create(2025, 1, 1, 0, 0, 0);
        $end   = Carbon::create(2025, 2, 15, 23, 0, 0);

        $batch = [];
        $batchSize = 500;

        // loop per jam; per iterasi kita AMBIL string waktu saat itu
        for ($time = $start->copy(); $time->lte($end); $time->addHour()) {
            // ambil string waktu sekarang (freeze value) sebelum memodifikasi $time lagi
            $ts = $time->format('Y-m-d H:i:s');

            $batch[] = [
                'suhu'       => rand(250, 320) / 10,   // 25.0–32.0
                'do'         => rand(60, 90) / 10,     // 6.0–9.0
                'ph'         => rand(60, 85) / 10,     // 6.0–8.5
                'amonia'     => rand(1, 10) / 10,      // 0.1–1.0
                'kekeruhan'  => rand(10, 50),          // NTU
                'created_at' => $ts,
                'updated_at' => $ts,
            ];

            // insert per batch agar efisien
            if (count($batch) >= $batchSize) {
                DB::table('sensor_data')->insert($batch);
                $batch = [];
            }
        }

        // insert sisa
        if (!empty($batch)) {
            DB::table('sensor_data')->insert($batch);
        }

        $total = DB::table('sensor_data')->count();
        $this->command->info("✅ Seeder selesai. Total rows now: {$total}");



        $startDate = Carbon::now()->subWeeks(13); // mulai dari 13 minggu yang lalu
        $biomassa = 1.2; // biomassa awal (kg)
        $quantity = 10;  // jumlah awal

        for ($i = 0; $i < 14; $i++) {
            Population::create([
                'quantity' => $quantity,
                'biomassa' => round($biomassa, 2),
                'waktu' => $startDate->copy()->addWeeks($i)->toDateString(),
            ]);

            // simulasi pertumbuhan biomassa & populasi tiap minggu
            $biomassa += mt_rand(8, 15) / 100; // naik 0.08–0.15 kg/minggu
            $quantity += mt_rand(1, 3);        // naik 1–3 ekor/minggu
        }
    }
}
