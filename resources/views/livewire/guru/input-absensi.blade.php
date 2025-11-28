<div>
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Input Absensi</h2>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Schedule Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal Pelajaran</label>
                <select wire:model.live="jadwal_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Pilih Jadwal</option>
                    @foreach($jadwalOptions as $option)
                        <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
                @error('jadwal_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Date Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" wire:model="tanggal" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('tanggal') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Quick Actions --}}
        @if(count($siswaList) > 0)
            <div class="mt-4 flex gap-2">
                <button wire:click="setAllStatus('hadir')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Semua Hadir
                </button>
                <button wire:click="setAllStatus('alpha')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Semua Alpha
                </button>
            </div>
        @endif
    </div>

    {{-- Student List --}}
    @if($isLoading)
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-center text-gray-600">Loading...</p>
        </div>
    @elseif(count($siswaList) > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NIS</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach($siswaList as $index => $siswa)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $siswa['nis'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $siswa['nama'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select wire:model="siswaList.{{ $index }}.status" class="border-gray-300 rounded-lg text-sm">
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </td>
                        <td class="px-6 py-4">
                            <input type="text" wire:model="siswaList.{{ $index }}.keterangan"
                                   placeholder="Keterangan (opsional)"
                                   class="w-full border-gray-300 rounded-lg text-sm">
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <button wire:click="simpan" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-medium">
                Simpan Absensi
            </button>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-center text-gray-600">Pilih jadwal untuk menampilkan daftar siswa</p>
        </div>
    @endif
</div>
