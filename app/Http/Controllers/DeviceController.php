<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        return view('device-control', compact('devices'));
    }

    public function toggle(Device $device)
    {
        $newStatus = $device->status === "ON" ? "OFF" : "ON";
        $device->update(['status' => $newStatus]);

        $server = '7db03374f5cf40628db4587fa2a91962.s1.eu.hivemq.cloud';
        $port = 8883;
        $clientId = 'laravel-subscribe';
        $username = 'nexdev';
        $password = 'nexYan1234';

        $mqtt = new MqttClient($server, $port, $clientId);

        $settings = (new ConnectionSettings)
            ->setUsername($username)
            ->setPassword($password)
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('kolam/lobster/lastwill')
            ->setLastWillMessage('Client disconnected unexpectedly')
            ->setLastWillQualityOfService(1);

        $mqtt->connect($settings, true);

        // Publish perintah ON/OFF
        $topic = "kolam/lobster/control/{$device->name}";
        $payload = json_encode(['status' => $newStatus]);

        $mqtt->publish($topic, $payload, 0);

        // Tutup koneksi
        $mqtt->disconnect();
    }
}
