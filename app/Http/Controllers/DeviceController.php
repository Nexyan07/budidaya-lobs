<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Population;
use Illuminate\Http\Request;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        $population = Population::orderBy('waktu', 'desc')->get();
        return view('device-control', compact('devices', 'population'));
    }

    public function updateBulk(Request $request)
    {
        $devicesInput = $request->input('devices');

        $server = '7db03374f5cf40628db4587fa2a91962.s1.eu.hivemq.cloud';
        $port = 8883;
        $clientId = 'laravel-subscribe';
        $username = 'nexdev';
        $password = 'nexYan1234';

        try {
            $mqtt = new MqttClient($server, $port, $clientId);

            $settings = (new ConnectionSettings)
                ->setUsername($username)
                ->setPassword($password)
                ->setKeepAliveInterval(60)
                ->setLastWillTopic('kolam/lobster/lastwill')
                ->setLastWillMessage('Client disconnected unexpectedly')
                ->setLastWillQualityOfService(1)
                ->setUseTls(true);

            $mqtt->connect($settings, true);

            foreach ($devicesInput as $deviceId => $status) {
                $device = Device::find($deviceId);
                if (!$device) continue;

                $device->status = $status;
                $device->save();

                // Publish perintah ON/OFF
                $topic = "kolam/lobster/control/{$device->name}";
                $payload = json_encode(['status' => $status]);

                $mqtt->publish($topic, $payload, 0);
            }

            // Tutup koneksi
            $mqtt->disconnect();

            return redirect()->back()->with("message", "Berhasil");
        } catch (MqttClientException $e) {
            Log::error("MQTT publish failed : " . $e->getMessage());
        }
    }
}
