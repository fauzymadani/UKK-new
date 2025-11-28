<div>
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Rekap Absensi</h2>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal Pelajaran</label>
                <select wire:model.live="jadwal_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">Semua Jadwal</option>
                    @foreach($jadwalOptions as $option)
                        <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" wire:model.live="tanggal_mulai" class="w-full border-gray-300 rounded-lg shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" wire:model.live="tanggal_selesai" class="w-full border-gray-300 rounded-lg shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model.live="status_filter" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir">Hadir</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>
        </div>

        <div class="mt-4">
            <button wire:click="resetFilters" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Reset Filter
            </button>
        </div>
    </div>

    {{-- Summary Statistics --}}
    @if(isset($summary['total']) && $summary['total'] > 0)
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Total</p>
                <p class="text-2xl font-bold text-gray-800">{{ $summary['total'] }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-4">
                <p class="text-sm text-green-600">Hadir</p>
                <p class="text-2xl font-bold text-green-800">{{ $summary['hadir'] }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg shadow p-4">
                <p class="text-sm text-yellow-600">Izin</p>
                <p class="text-2xl font-bold text-yellow-800">{{ $summary['izin'] }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg shadow p-4">
                <p class="text-sm text-blue-600">Sakit</p>
                <p class="text-2xl font-bold text-blue-800">{{ $summary['sakit'] }}</p>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-4">
                <p class="text-sm text-red-600">Alpha</p>
                <p class="text-2xl font-bold text-red-800">{{ $summary['alpha'] }}</p>
            </div>
        </div>

        <div class="bg-indigo-50 rounded-lg shadow p-4 mb-6">
            <p class="text-sm text-indigo-600">Persentase Kehadiran</p>
            <div class="flex items-center mt-2">
                <div class="flex-1 bg-gray-200 rounded-full h-4 mr-4">
                    <div class="bg-indigo-600 h-4 rounded-full" style="width: {{ $summary['persentase_hadir'] }}%"></div>
                </div>
                <span class="text-2xl font-bold text-indigo-800">{{ $summary['persentase_hadir'] }}%</span>
            </div>
        </div>
    @endif

    {{-- Attendance Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($absensi as $data)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($data->tanggal)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $data->siswa->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $data->siswa->nis }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $data->jadwalPelajaran->kelas->nama_kelas }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $data->jadwalPelajaran->mataPelajaran->nama_mapel }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'hadir' => 'bg-green-100 text-green-800',
                                'izin' => 'bg-yellow-100 text-yellow-800',
                                'sakit' => 'bg-blue-100 text-blue-800',
                                'alpha' => 'bg-red-100 text-red-800',
                            ];
                            $statusLabels = [
                                'hadir' => 'Hadir',
                                'izin' => 'Izin',
                                'sakit' => 'Sakit',
                                'alpha' => 'Alpha',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$data->status] }}">
                                {{ $statusLabels[$data->status] }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $data->waktu_absen ? \Carbon\Carbon::parse($data->waktu_absen)->format('H:i') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-600">
                        Tidak ada data absensi
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-6 py-4 bg-gray-50">
            {{ $absensi->links() }}
        </div>
    </div>
</div>
