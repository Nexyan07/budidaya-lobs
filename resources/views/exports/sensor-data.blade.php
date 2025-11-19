@php
    use Carbon\Carbon;

    // Konversi nama bulan ke Bahasa Indonesia
    $bulanIndo = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $judul = 'Data Sensor';

    if ($level === 'month') {
        $judul .= ' per Bulan';
    } elseif ($level === 'day' && $parent) {
        // $parent format: 2025-01
        [$tahun, $bulan] = explode('-', $parent);
        $judul .= ' Bulan ' . ($bulanIndo[$bulan] ?? $bulan) . ' ' . $tahun;
    } elseif ($level === 'hour' && $parent) {
        // $parent format: 2025-01-03
        try {
            $tanggal = Carbon::parse($parent);
            $judul .= ' Tanggal ' . $tanggal->format('j ') . ($bulanIndo[$tanggal->format('m')] ?? $tanggal->format('m')) . ' ' . $tanggal->format('Y');
        } catch (\Exception $e) {
            $judul .= ' (Format tanggal tidak valid)';
        }
    }
@endphp

<h3 style="text-align:center; font-weight:bold; margin-bottom:12px;">
    {{ $judul }}
</h3>

<table style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr>
            <th style="border: 1px solid #000; background: #f2f2f2; font-weight: bold; text-align: center;">Waktu</th>
            <th style="border: 1px solid #000; background: #f2f2f2; font-weight: bold; text-align: center;">Suhu (°C)</th>
            <th style="border: 1px solid #000; background: #f2f2f2; font-weight: bold; text-align: center;">DO (mg/L)</th>
            <th style="border: 1px solid #000; background: #f2f2f2; font-weight: bold; text-align: center;">pH</th>
            <th style="border: 1px solid #000; background: #f2f2f2; font-weight: bold; text-align: center;">Amonia (mg/L)</th>
            <th style="border: 1px solid #000; background: #f2f2f2; font-weight: bold; text-align: center;">Kekeruhan (NTU)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            @php
                $formatValue = function ($value, $min, $max) {
                    if (!is_null($value)) {
                        return round($value, 2);
                    }
                    if (!is_null($min) && $min == $max) {
                        return round($min, 2);
                    }
                    if (!is_null($min) && !is_null($max)) {
                        return round($min, 2) . ' - ' . round($max, 2);
                    }
                    return '-';
                };
            @endphp

            <tr>
                <td style="border: 1px solid #000; text-align: center;">{{ $row->waktu }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $formatValue($row->suhu ?? null, $row->suhu_min ?? null, $row->suhu_max ?? null) }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $formatValue($row->do ?? null, $row->do_min ?? null, $row->do_max ?? null) }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $formatValue($row->ph ?? null, $row->ph_min ?? null, $row->ph_max ?? null) }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $formatValue($row->amonia ?? null, $row->amonia_min ?? null, $row->amonia_max ?? null) }}</td>
                <td style="border: 1px solid #000; text-align: center;">{{ $formatValue($row->kekeruhan ?? null, $row->kekeruhan_min ?? null, $row->kekeruhan_max ?? null) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
