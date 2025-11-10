<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Manual Perangkat</title>
    @vite(['resources/css/app.css', 'resources/js/device-control.js'])
</head>

<body class="bg-blue-50 min-h-screen text-gray-900">

    <div x-data="deviceControl" x-init="init()" class="p-4 sm:p-6">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-200">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between gap-y-4 border-b border-gray-300 px-5 py-4 ">
              <div class="flex gap-2 sm:gap-4 items-center">
                  <button onclick="history.back()" class="flex gap-2 mt-1 hover:bg-gray-200 rounded-full p-1">
                      <i data-lucide="chevron-left" class="size-5 text-blue-700"></i>
                  </button>
                    <h1 class="text-xl font-semibold text-blue-700 flex items-center gap-2">
                      Kontrol Manual Perangkat
                    </h1>
                </div>
                <button @click="handleSchedule"
                    class="bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center gap-2 px-4 py-2 rounded-lg">
                    <i data-lucide="timer" class="w-4 h-4"></i> Atur Jadwal
                </button>
            </div>

            <!-- Kontrol Perangkat -->
            <div class="p-5 space-y-4">
                <template x-for="(device, key) in devices" :key="key">
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-white rounded-2xl shadow-sm border border-gray-200">
                        <div class="w-full">
                            <div class="flex justify-between w-full mb-1">
                                <h3 class="font-semibold text-blue-700 text-lg flex items-center gap-2">
                                    <i :data-lucide="'power'" :class="device.active ? 'text-blue-600' : 'text-gray-400'"
                                        class="w-5 h-5"></i>
                                    <span x-text="device.label"></span>
                                </h3>
                                <button @click="toggleDevice(key)"
                                    class="relative w-12 h-6 flex items-center rounded-full transition"
                                    :class="device.active ? 'bg-blue-600' : 'bg-gray-300'">
                                    <span
                                        class="absolute left-1 w-4 h-4 bg-white rounded-full shadow transform transition"
                                        :class="device.active ? 'translate-x-6' : 'translate-x-0'"></span>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600" x-text="device.description"></p>
                        </div>
                    </div>
                </template>

                <div class="pt-4 flex justify-end">
                    <button @click="saveSettings"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
