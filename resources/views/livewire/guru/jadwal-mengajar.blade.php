<div>
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Jadwal Mengajar</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($hariList as $hari)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 bg-indigo-50">
                    <h3 class="text-lg font-semibold text-indigo-800">{{ $hari }}</h3>
                </div>
                <div class="p-6">
                    @if(count($jadwalPerHari[$hari]) > 0)
                        <div class="space-y-3">
                            @foreach($jadwalPerHari[$hari] as $jadwal)
                                <div class="border-l-4 border-indigo-500 pl-4 py-2">
                                    <p class="font-semibold text-gray-800">{{ $jadwal['mata_pelajaran']['nama_mapel'] }}</p>
                                    <p class="text-sm text-gray-600">{{ $jadwal['kelas']['nama_kelas'] }}</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($jadwal['jam_mulai'])->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($jadwal['jam_selesai'])->format('H:i') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">Tidak ada jadwal</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
