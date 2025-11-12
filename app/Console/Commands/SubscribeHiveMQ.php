<?php

namespace App\Console\Commands;

use App\Models\SensorData;
use Illuminate\Console\Command;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class SubscribeHiveMQ extends Command
{
    protected $signature = 'mqtt:subscribe';

    protected $description = 'Subscribe ke topik HiveMQ untuk menerima data sensor';

    public function handle()
    {
        $server = '7db03374f5cf40628db4587fa2a91962.s1.eu.hivemq.cloud';
        $port = 8883;
        $clientId = 'laravel-subscribe';
        $username = 'nexdev';
        $password = 'nexYan1234';

        $connectionSettings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setUseTls(true);

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect($connectionSettings, true);

        $this->info("Berhasil terhubung");
        $this->info('Menunggu topic ...');

        $mqtt->subscribe('kolam/lobster/data', function (string $topic, string $message) {
            $this->info("pesan diterima dari [$topic]: $message");

            $data = json_decode($message, true);

            if (!$data) {
                echo "❌ Gagal decode JSON\n";
                return;
            }

            try {
                SensorData::create([
                    'suhu' => $data['suhu'] ?? null,
                    'do' => $data['do'] ?? null,
                    'ph' => $data['ph'] ?? null,
                    'amonia' => $data['amonia'] ?? null,
                    'kekeruhan' => $data['kekeruhan'] ?? null,
                ]);
            } catch (\Exception $e) {
                echo "gagal" . $e->getMessage() . "\n";
            }
        }, 0);

        $mqtt->loop(true);
    }
}
