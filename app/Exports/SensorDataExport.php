<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class SensorDataExport implements FromView, WithColumnWidths
{
    protected $data;
    protected $level;
    protected $parent;

    public function __construct($data, $level, $parent = null)
    {
        $this->data = $data;
        $this->level = $level;
        $this->parent = $parent;
    }

    public function view(): View
    {
        return view('exports.sensor-data', [
            'data' => $this->data,
            'level' => $this->level,
            'parent' => $this->parent,
        ]);
    }

     public function columnWidths(): array
    {
        return [
            'A' => 25, // Waktu
            'B' => 20, // Suhu
            'C' => 20, // DO
            'D' => 15, // pH
            'E' => 20, // Amonia
            'F' => 25, // Kekeruhan
        ];
    }
}
