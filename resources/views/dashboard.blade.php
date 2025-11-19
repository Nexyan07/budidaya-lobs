<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-blue-700 leading-tight">
            {{ __('Dashboard Kualitas Air') }}
        </h2>
        <button class="text-white bg-blue-600 px-3 my-1 rounded-md py-1">Refresh</button>
    </x-slot>

    <div class="bg-blue-50 px-2 md:px-4 lg:px-6 min-h-screen" x-data="dashboard({{ $population->toJson() }})" x-init="init()">

        <!-- Sensor Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
            @foreach ($sensors as $sensor)
                <div
                    class="p-4 rounded-2xl shadow bg-white flex items-center gap-2 border-l-4
                    @if ($sensor['status'] === 'normal') border-blue-500
                    @elseif($sensor['status'] === 'warning') border-yellow-400
                    @else border-red-500 @endif">
                    <i data-lucide="{{ $sensor['icon'] }}"
                        class="w-6 h-6
                        @if ($sensor['status'] === 'normal') text-blue-600
                        @elseif($sensor['status'] === 'warning') text-yellow-500
                        @else text-red-600 @endif"></i>
                    <div class="">
                        <div class="font-medium text-gray-800 text-sm">{{ $sensor['label'] }}</div>
                        <div
                            class="text-lg font-semibold
                            @if ($sensor['status'] === 'normal') text-blue-600
                            @elseif($sensor['status'] === 'warning') text-yellow-500
                            @else text-red-600 @endif">
                            {{ $sensor['value'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chart -->
        <!-- Populasi & Status Perangkat -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

            <!-- Kartu Populasi Lobster -->
            <div class="bg-white p-5 rounded-2xl shadow-md">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 mb-3">
                        <i data-lucide="fish" class="w-5 h-5 text-blue-600"></i>
                        <h2 class="text-lg font-semibold text-blue-600">Populasi Lobster</h2>
                    </div>
                    @if (Auth::check() && Auth::user()->role === 'admin')
                        <a href="/control#population" class="hover:bg-blue-50 p-2 rounded-full">
                            <i data-lucide="pencil-line" class="size-5 text-slate-600"></i>
                        </a>
                    @endif
                </div>

                <div class="text-3xl font-bold text-blue-700">{{ $population->first()->quantity }}</div>
                <p class="text-gray-500 mb-3">Estimasi Biomassa: <span class="text-blue-600">{{ $population->first()->biomassa }} kg</span></p>

                <div class="h-24">
                    <canvas id="biomassChart"></canvas>
                </div>
            </div>

            <!-- Kartu Status Perangkat -->
            <div class="bg-white p-5 rounded-2xl shadow-md">
                <h2 class="text-lg font-semibold text-blue-600 mb-4">Status Perangkat</h2>

                @foreach ($devices as $device)
                    <div class="flex justify-between items-center px-3 py-2 border rounded-xl mb-2 bg-gray-50">
                        <span class="font-medium text-gray-800">{{ $device['name'] }}</span>
                        <span
                            class="px-3 py-1 text-xs font-semibold rounded-full
                            @if ($device['status'] === 'ON') bg-blue-100 text-blue-700
                            @elseif ($device['status'] === 'OFF') bg-gray-200 text-gray-500 @endif">{{ $device['status'] }}</span>
                    </div>
                @endforeach

                @if (Auth::check() && Auth::user()->role === 'admin')
                    <a href="/control"
                        class="block text-center w-full bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg py-2 mt-3">
                        Kontrol Manual
                    </a>
                @endif
            </div>
        </div>

        <!-- Notifikasi -->
        <div class="bg-white p-5 rounded-2xl shadow-md">
            <div class="flex items-center gap-2 mb-3">
                <i data-lucide="alert-triangle" class="text-yellow-500 w-5 h-5"></i>
                <h2 class="text-lg font-semibold text-blue-700">Notifikasi Terbaru</h2>
            </div>
            <template x-if="alerts.length === 0">
                <p class="text-gray-500">Tidak ada notifikasi aktif.</p>
            </template>
            <ul>
                <template x-for="alert in alerts" :key="alert.id">
                    <li class="border rounded-xl p-3 mb-2 flex justify-between bg-blue-50">
                        <span class="text-blue-700 font-medium" x-text="alert.message"></span>
                        <span class="text-sm text-gray-500" x-text="alert.time"></span>
                    </li>
                </template>
            </ul>
        </div>

        <!-- Data History -->
        <div x-data="historyViewer()" x-init="init()" class="bg-white shadow-md rounded-2xl p-5 mt-6">
            <div class="flex flex-col sm:flex-row justify-between gap-3 mb-3">
                <h2 class="text-xl font-semibold text-blue-700 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5"></i> Riwayat Data Sensor
                </h2>
                @if (Auth::check() && Auth::user()->role === 'admin')
                    <button type="button"
                        @click="window.location.href = '/export-histories?level=' + level + (parent ? '&parent=' + parent : '')"
                        class="max-sm:flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg flex items-center justify-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i> Export Excel
                    </button>
                @endif
            </div>

            <div class="overflow-x-auto">
                <template x-if="level !== 'month'">
                    <button @click="goBack" class="flex gap-2 mb-2" x-init="createIcons({ icons: lucideIcons })">
                        <i data-lucide="chevron-left"></i>
                        Kembali
                    </button>
                </template>
                <table class="min-w-full text-sm border border-gray-200 rounded-xl">
                    <thead class="bg-blue-600 text-white">
                        <tr class="border-b border-white whitespace-nowrap">
                            <th class="py-2 px-4 text-left">Waktu</th>
                            <th class="py-2 px-4 text-left">Suhu (°C)</th>
                            <th class="py-2 px-4 text-left">DO (mg/L)</th>
                            <th class="py-2 px-4 text-left">pH</th>
                            <th class="py-2 px-4 text-left">Amonia (mg/L)</th>
                            <th class="py-2 px-4 text-left">Kekeruhan (NTU)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="histories.length > 0">
                            <template x-for="history in histories" :key="history.waktu">
                                {{-- <p x-text="history.waktu"></p> --}}
                                <tr @click="level !== 'hour' ? loadNext(history.waktu) : ''"
                                    class="even:bg-blue-50 text-gray-700 hover:bg-blue-600 hover:text-white whitespace-nowrap">
                                    <td class="py-2 px-4 font-medium" x-text="formatLabel(history.waktu)">
                                    </td>
                                    <td class="py-2 px-4"
                                        x-text="level === 'hour' ? history.suhu + ' ' : history.suhu_min + ' - ' + history.suhu_max">
                                    </td>
                                    <td class="py-2 px-4"
                                        x-text="level === 'hour' ? history.do : history.do_min + ' - ' + history.do_max">
                                    </td>
                                    <td class="py-2 px-4"
                                        x-text="level === 'hour' ? history.ph : history.ph_min + ' - ' + history.ph_max">
                                    </td>
                                    <td class="py-2 px-4"
                                        x-text="level === 'hour' ? history.amonia : history.amonia_min + ' - ' + history.amonia_max">
                                    </td>
                                    <td class="py-2 px-4"
                                        x-text="level === 'hour' ? history.kekeruhan : history.kekeruhan_min + ' - ' + history.kekeruhan_max">
                                    </td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Kirim data dari backend ke JS
        window.historyData = @json($histories);
        window.historyLevel = '{{ $level }}';
        window.historyParent = @json($parent);
    </script>

</x-app-layout>
