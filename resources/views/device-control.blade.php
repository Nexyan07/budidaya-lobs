<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol Manual Perangkat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-blue-50 min-h-screen text-gray-900">
    <div class="lg:flex">
        <div class="flex flex-col mx-auto px-2">
            <a href={{ route('dashboard') }} class="inline-flex mt-4 gap-2 items-center text-gray-800 py-1 mb-4">
                <i data-lucide="chevron-left" class="size-7"></i>
                <span>Dashboard</span>
            </a>
            <div class="flex flex-col-reverse lg:flex-row gap-x-4 gap-y-6 mb-8">
                <div id="population" class="flex-1 w-full" x-data="{ openModal: false, editMode: false, form: { id: '', quantity: '', biomassa: '', waktu: '' } }">

                    <!-- ===== DATA SAMPLES ===== -->
                    <div class="bg-white shadow rounded-lg h-full  px-2">
                        <div class="flex items-center justify-between py-4 px-3">
                            <h2 class="text-xl font-semibold text-blue-700">Data Populasi</h2>
                            <button
                                @click="editMode = false; form = {id:'', quantity:'', biomassa:'', waktu:''}; openModal = true"
                                class="flex gap-2 items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                <i data-lucide="plus" class="size-5"></i>
                                Tambah
                            </button>
                        </div>
                        <div class="overflow-auto max-h-96">
                            <table class="text-sm text-left whitespace-nowrap">
                                <thead class="bg-blue-600 text-white">
                                    <tr class="">
                                        <th class="px-4 py-2">NO</th>
                                        <th class="px-4 py-2">Quantity</th>
                                        <th class="px-4 py-2">Biomassa</th>
                                        <th class="px-4 py-2">Waktu</th>
                                        <th class="px-4 py-2 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($population as $p)
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                            <td class="px-4 py-2">{{ $p->quantity }}</td>
                                            <td class="px-4 py-2">{{ $p->biomassa }} kg</td>
                                            <td class="px-4 py-2">{{ $p->waktu }}</td>
                                            <td class="px-4 py-2 text-center space-x-2">
                                                <button
                                                    @click="editMode = true; openModal = true; form = {id:'{{ $p->id }}', quantity:'{{ $p->quantity }}', biomassa:'{{ $p->biomassa }}', waktu:'{{ $p->waktu }}'}"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded">Edit</button>
                                                <form action="{{ route('populations.destroy', $p->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Yakin hapus data ini?')"
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ===== MODAL TAMBAH/EDIT SAMPLE ===== -->
                    <div x-show="openModal" x-cloak
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                        <div @click.away="openModal = false" class="bg-white w-full max-w-md p-6 rounded-lg shadow-lg">
                            <h2 class="text-xl font-semibold mb-4"
                                x-text="editMode ? 'Edit Data Sample' : 'Tambah Data Sample'"></h2>

                            <form
                                :action="editMode ? '{{ url('populations') }}/' + form.id : '{{ route('populations.store') }}'"
                                method="POST">
                                @csrf
                                <template x-if="editMode">
                                    <input type="hidden" name="_method" value="PUT">
                                </template>

                                <div class="mb-4">
                                    <label class="block mb-1 font-medium">Quantity</label>
                                    <input type="number" name="quantity" x-model="form.quantity" required
                                        class="w-full border-gray-300 rounded-lg p-2">
                                </div>

                                <div class="mb-4">
                                    <label class="block mb-1 font-medium">Biomassa</label>
                                    <input type="number" step="0.01" name="biomassa" x-model="form.biomassa"
                                        required class="w-full border-gray-300 rounded-lg p-2">
                                </div>

                                <div class="mb-4">
                                    <label class="block mb-1 font-medium">Waktu</label>
                                    <input type="date" name="waktu" x-model="form.waktu" required
                                        class="w-full border-gray-300 rounded-lg p-2">
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" @click="openModal = false"
                                        class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">Batal</button>
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                                        x-text="editMode ? 'Update' : 'Simpan'"></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div x-data="deviceControl({{ json_encode($devices) }})" x-init="init()"
                    class="bg-white lg:w-[26rem] rounded-2xl shadow-md overflow-hidden border border-gray-200">
                    <!-- Header -->
                    <div class="flex justify-between gap-y-4 border-b border-gray-300 px-5 py-4 ">
                        <div class="flex gap-2 sm:gap-4 items-center">
                            <h1 class="text-xl font-semibold text-blue-700 flex items-center gap-2">
                                Kontrol Alat
                            </h1>
                        </div>
                        <button @click="handleSchedule"
                            class="bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center gap-2 px-4 py-2 rounded-lg">
                            <i data-lucide="timer" class="size-5"></i> Jadwal
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
                                                :class="devices[{{ $key }}].status === 'ON' ? 'text-blue-600' :
                                                    'text-gray-400'"
                                                class="w-5 h-5"></i>
                                            <span>{{ $device['name'] }}</span>
                                        </h3>
                                        <button type="button" @click="toggleDevice({{ $key }})"
                                            class="relative w-12 h-6 flex items-center rounded-full transition"
                                            :class="devices[{{ $key }}].status === 'ON' ? 'bg-blue-600' :
                                                'bg-gray-300'">
                                            <span
                                                class="absolute left-1 w-4 h-4 bg-white rounded-full shadow transform transition"
                                                :class="devices[{{ $key }}].status === 'ON' ? 'translate-x-6' :
                                                    'translate-x-0'"></span>
                                        </button>
                                        <input type="hidden"
                                            :name="'devices[' + devices[{{ $key }}].id + ']'"
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
        </div>
    </div>
</body>

</html>
