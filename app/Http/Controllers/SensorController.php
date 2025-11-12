<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    public function index()
    {
        $latest = SensorData::latest()->first();
        if (isset($latest)) {
            $sensors = [
                [
                    'icon' => 'thermometer',
                    'label' => 'suhu',
                    'value' => number_format($latest->suhu, 1) . " °C",
                    'status' => $this->status($latest->suhu, 'suhu')
                ],
                [
                    'icon' => 'droplets',
                    'label' => 'DO',
                    'value' => number_format($latest->do, 1) . " mg/L",
                    'status' => $this->status($latest->do, 'do')
                ],
                [
                    'icon' => 'activity',
                    'label' => 'pH',
                    'value' => number_format($latest->ph, 1),
                    'status' => $this->status($latest->ph, 'ph')
                ],
                [
                    'icon' => 'beaker',
                    'label' => 'amonia',
                    'value' => number_format($latest->amonia, 2) . " mg/L",
                    'status' => $this->status($latest->amonia, 'amonia')
                ],
                [
                    'icon' => 'cloud',
                    'label' => 'kekeruhan',
                    'value' => number_format($latest->kekeruhan, 0) . " NTU",
                    'status' => $this->status($latest->kekeruhan, 'kekeruhan')
                ],
            ];
        } else {
            $sensors = [
                [
                    'icon' => 'thermometer',
                    'label' => 'suhu',
                    'value' => "0 °C",
                    'status' => 'normal',
                ],
                [
                    'icon' => 'droplets',
                    'label' => 'DO',
                    'value' => "0 mg/L",
                    'status' => 'normal',
                ],
                [
                    'icon' => 'activity',
                    'label' => 'pH',
                    'value' => '0',
                    'status' => 'normal'
                ],
                [
                    'icon' => 'beaker',
                    'label' => 'amonia',
                    'value' => "0 mg/L",
                    'status' => 'normal'
                ],
                [
                    'icon' => 'cloud',
                    'label' => 'kekeruhan',
                    'value' => "0 NTU",
                    'status' => 'normal',
                ],
            ];
        }

        $devices = Device::all();
        // $history = SensorData::orderBy('desc')->take(50)->get();

        return view('dashboard', compact('sensors', 'devices'));
    }

    private function status($value, $type)
    {
        switch ($type) {
            case 'suhu':
                if ($value < 25 || $value > 32) return 'warning';
                return 'normal';
            case 'do':
                if ($value < 5) return 'warning';
                return 'normal';
            case 'ph':
                if ($value < 6.5 || $value > 8.5) return 'danger';
                return 'normal';
            case 'amonia':
                if ($value > 0.05) return 'danger';
                return 'normal';
            case 'kekeruhan':
                if ($value > 20) return 'warning';
                return 'normal';
            default:
                return 'normal';
        }
    }

    public function store(Request $request)
    {
        SensorData::create([
            'suhu' => $request->suhu,
            'do' => $request->do,
            'ph' => $request->ph,
            'amonia' => $request->amonia,
            'kekeruhan' => $request->kekeruhan,
        ]);

        return response()->json(['status' => "ok"]);
    }
}
