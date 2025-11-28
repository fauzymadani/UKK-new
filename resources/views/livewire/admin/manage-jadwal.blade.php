<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Kelola Jadwal Pelajaran</h2>
        <button wire:click="create"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah Jadwal
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" wire:model.live="search" placeholder="Cari kelas atau mata pelajaran..."
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <select wire:model.live="hariFilter" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">Semua Hari</option>
                    @foreach($hariList as $hari)
                        <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Jadwal Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hari</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kelas</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guru</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($jadwal as $j)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-indigo-100 text-indigo-800">
                                {{ $j->hari }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $j->kelas->nama_kelas }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $j->mataPelajaran->nama_mapel }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $j->mataPelajaran->guru->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $j->jam_mulai->format('H:i') }} - {{ $j->jam_selesai->format('H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit({{ $j->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            Edit
                        </button>
                        <button wire:click="delete({{ $j->id }})"
                                onclick="return confirm('Yakin ingin menghapus jadwal ini?')"
                                class="text-red-600 hover:text-red-900">
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-600">
                        Tidak ada data jadwal
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 bg-gray-50">
            {{ $jadwal->links() }}
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $editMode ? 'Edit Jadwal' : 'Tambah Jadwal' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                            <select wire:model="kelas_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">Pilih Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas['id'] }}">{{ $kelas['nama_kelas'] }}</option>
                                @endforeach
                            </select>
                            @error('kelas_id') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                            <select wire:model="mata_pelajaran_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">Pilih Mata Pelajaran</option>
                                @foreach($mapelList as $mapel)
                                    <option value="{{ $mapel['id'] }}">
                                        {{ $mapel['nama_mapel'] }} ({{ $mapel['guru']['name'] }})
                                    </option>
                                @endforeach
                            </select>
                            @error('mata_pelajaran_id') <span
                                class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hari</label>
                            <select wire:model="hari" class="w-full border-gray-300 rounded-lg shadow-sm">
                                @foreach($hariList as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                            @error('hari') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                                <input type="time" wire:model="jam_mulai"
                                       class="w-full border-gray-300 rounded-lg shadow-sm">
                                @error('jam_mulai') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                                <input type="time" wire:model="jam_selesai"
                                       class="w-full border-gray-300 rounded-lg shadow-sm">
                                @error('jam_selesai') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                    <button wire:click="closeModal"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                        Batal
                    </button>
                    <button wire:click="save" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
