<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SensorDataExport;
use App\Models\Population;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
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
        // $latestPopulation = Population::orderBy('waktu', 'desc')->first();
        $population= Population::orderBy('waktu', 'desc')->limit(7)->get();
        $histories = $this->getDataGrouped('month');

        return view('dashboard', compact('sensors', 'devices', 'population', 'histories'))
            ->with(['level' => 'month', 'parent' => null]);
    }

    public function showHistories(Request $request)
    {
        $level = $request->get('level', 'month');
        $parent = $request->get('parent');

        $histories = $this->getDataGrouped($level, $parent);

        return response()->json([
            'histories' => $histories,
            'level' => $level,
            'parent' => $parent,
        ]);
    }

    private function getDataGrouped(string $level, ?string $parent = null)
    {
        $query = \App\Models\SensorData::query();

        // --- Level: bulan ---
        if ($level === 'month') {
            return $query->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m") as waktu,
                MIN(suhu) as suhu_min, MAX(suhu) as suhu_max,
                MIN(do) as do_min, MAX(do) as do_max,
                MIN(ph) as ph_min, MAX(ph) as ph_max,
                MIN(amonia) as amonia_min, MAX(amonia) as amonia_max,
                MIN(kekeruhan) as kekeruhan_min, MAX(kekeruhan) as kekeruhan_max
            ')
                ->groupBy('waktu')
                ->orderBy('waktu')
                ->get();
        }

        // --- Level: hari ---
        if ($level === 'day' && $parent) {
            return $query->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$parent])
                ->selectRaw('
                DATE_FORMAT(created_at, "%Y-%m-%d") as waktu,
                MIN(suhu) as suhu_min, MAX(suhu) as suhu_max,
                MIN(do) as do_min, MAX(do) as do_max,
                MIN(ph) as ph_min, MAX(ph) as ph_max,
                MIN(amonia) as amonia_min, MAX(amonia) as amonia_max,
                MIN(kekeruhan) as kekeruhan_min, MAX(kekeruhan) as kekeruhan_max
            ')
                ->groupBy('waktu')
                ->orderBy('waktu')
                ->get();
        }

        // --- Level: jam ---
        if ($level === 'hour' && $parent) {
            return $query->whereRaw('DATE(created_at) = ?', [$parent])
                ->selectRaw('
                DATE_FORMAT(created_at, "%H:00") as waktu,
                suhu,
                do,
                ph,
                amonia,
                kekeruhan
            ')
                ->orderBy('created_at')
                ->get();
        }

        return collect();
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

    public function export(Request $request)
    {
        $level = $request->get('level', 'month');
        $parent = $request->get('parent', null);

        $data = $this->getDataGrouped($level, $parent);
        
        $filename = "sensor_data_{$level}.xlsx";

        return Excel::download(new SensorDataExport($data, $level, $parent), $filename);
    }
}
