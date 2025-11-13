<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Manual Perangkat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-blue-50 min-h-screen text-gray-900">

    <a href={{ route('dashboard') }}
        class="inline-flex mt-4 gap-2 items-center text-gray-800 hover:bg-blue-100 px-2 py-1 rounded-md transition-colors duration-300 mb-4 sm:mb-6">
        <i data-lucide="chevron-left" class="size-7"></i>
        <span>Kembali</span>
    </a>
    <div x-data="deviceControl({{ json_encode($devices) }})" x-init="init()" class="px-2 flex mx-auto justify-center">
        <div class="bg-white w-[26rem] rounded-2xl shadow-md overflow-hidden border border-gray-200">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between gap-y-4 border-b border-gray-300 px-5 py-4 ">
                <div class="flex gap-2 sm:gap-4 items-center">
                    <h1 class="text-xl font-semibold text-blue-700 flex items-center gap-2">
                        Kontrol Manual Perangkat
                    </h1>
                </div>
                <button @click="handleSchedule"
                    class="bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center gap-2 px-4 py-2 rounded-lg">
                    <i data-lucide="timer" class="w-4 h-4"></i> Jadwal
                </button>
            </div>

            <!-- Kontrol Perangkat -->
            <form action="{{ route('devices.updateBulk') }}" method="POST" class="p-5 space-y-4">
                @csrf
                @foreach ($devices as $key => $device)
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-white rounded-2xl shadow-sm border border-gray-200">
                        <div class="w-full">
                            <div class="flex justify-between w-full mb-1">
                                <h3 class="font-semibold text-blue-700 text-lg flex items-center gap-2">
                                    <i :data-lucide="'power'"
                                        :class="devices[{{ $key }}].status === 'ON' ? 'text-blue-600' : 'text-gray-400'"
                                        class="w-5 h-5"></i>
                                    <span>{{ $device['name'] }}</span>
                                </h3>
                                <button type="button" @click="toggleDevice({{ $key }})"
                                    class="relative w-12 h-6 flex items-center rounded-full transition"
                                    :class="devices[{{ $key }}].status === 'ON' ? 'bg-blue-600' : 'bg-gray-300'">
                                    <span
                                        class="absolute left-1 w-4 h-4 bg-white rounded-full shadow transform transition"
                                        :class="devices[{{ $key }}].status === 'ON' ? 'translate-x-6' :
                                            'translate-x-0'"></span>
                                </button>
                                <input type="hidden" :name="'devices[' + devices[{{ $key }}].id + ']'"
                                    :value="devices[{{ $key }}].status">
                            </div>
                            <p class="text-sm text-gray-600">{{ $device['description'] }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
