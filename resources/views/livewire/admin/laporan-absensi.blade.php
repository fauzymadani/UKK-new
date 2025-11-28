<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Laporan Absensi</h2>
        <button wire:click="export" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export Excel
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                <select wire:model.live="kelas_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas['id'] }}">{{ $kelas['nama_kelas'] }}</option>
                    @endforeach
                </select>
            </div>

            @if($kelas_id)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Siswa</label>
                    <select wire:model.live="siswa_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">Semua Siswa</option>
                        @foreach($siswaList as $siswa)
                            <option value="{{ $siswa['id'] }}">{{ $siswa['name'] }} ({{ $siswa['nis'] }})</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" wire:model.live="tanggal_mulai" class="w-full border-gray-300 rounded-lg shadow-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" wire:model.live="tanggal_selesai" class="w-full border-gray-300 rounded-lg shadow-sm">
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <select wire:model.live="status_filter" class="border-gray-300 rounded-lg shadow-sm">
                <option value="">Semua Status</option>
                <option value="hadir">Hadir</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
                <option value="alpha">Alpha</option>
            </select>

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
            <p class="text-sm text-indigo-600 mb-2">Persentase Kehadiran</p>
            <div class="flex items-center">
                <div class="flex-1 bg-gray-200 rounded-full h-4 mr-4">
                    <div class="bg-indigo-600 h-4 rounded-full" style="width: {{ $summary['persentase_hadir'] }}%"></div>
                </div>
                <span class="text-2xl font-bold text-indigo-800">{{ $summary['persentase_hadir'] }}%</span>
            </div>
        </div>
    @endif

    {{-- Absensi Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
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
                        <div class="text-xs text-gray-500">{{ $data->siswa->nis }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $data->siswa->kelas->nama_kelas }}
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
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$data->status] }}">
                                {{ ucfirst($data->status) }}
                            </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $data->keterangan ?? '-' }}
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

        <div class="px-6 py-4 bg-gray-50">
            {{ $absensi->links() }}
        </div>
    </div>
</div>
