<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
        <button wire:click="refresh" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
            Refresh
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Hadir</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistik['total_hadir'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Izin</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistik['total_izin'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Sakit</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistik['total_sakit'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Alpha</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statistik['total_alpha'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Schedule --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Jadwal Hari Ini</h3>
        </div>
        <div class="p-6">
            @if(count($jadwalHariIni) > 0)
                <div class="space-y-4">
                    @foreach($jadwalHariIni as $jadwal)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $jadwal['mata_pelajaran']['nama_mapel'] }}</p>
                                <p class="text-sm text-gray-600">{{ $jadwal['kelas']['nama_kelas'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($jadwal['jam_mulai'])->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($jadwal['jam_selesai'])->format('H:i') }}
                                </p>
                                <a href="{{ route('guru.input-absensi') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                                    Input Absensi →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-8">Tidak ada jadwal mengajar hari ini</p>
            @endif
        </div>
    </div>
</div>
