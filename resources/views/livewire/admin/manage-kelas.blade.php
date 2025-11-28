<div>
    {{-- Flash messages --}}
    @if(session()->has('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <input
                type="text"
                wire:model.debounce.300ms="search"
                placeholder="Search kelas or jurusan..."
                class="border rounded px-3 py-2 w-72"
            />
            <button wire:click="create" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                + Add Kelas
            </button>
        </div>

        <div>
            <span class="text-sm text-gray-600">Showing {{ $kelas->firstItem() ?? 0 }} - {{ $kelas->lastItem() ?? 0 }} of {{ $kelas->total() }}</span>
        </div>
    </div>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Nama Kelas</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Tingkat</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Jurusan</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Wali Kelas</th>
                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Actions</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            @forelse($kelas as $item)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->nama_kelas }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->tingkat }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->jurusan }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->waliKelas->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right space-x-2">
                        <button wire:click="edit({{ $item->id }})"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit
                        </button>
                        <button
                            wire:click="delete({{ $item->id }})"
                            onclick="confirm('Delete this class?') || event.stopImmediatePropagation()"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-600">No classes found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $kelas->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg w-full max-w-xl shadow-lg">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ $editMode ? 'Edit Kelas' : 'Create Kelas' }}</h3>
                    <button wire:click="closeModal" class="text-gray-600 hover:text-gray-800">&times;</button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Kelas</label>
                        <input type="text" wire:model.defer="nama_kelas" class="w-full mt-1 border rounded px-3 py-2"/>
                        @error('nama_kelas') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tingkat</label>
                            <select wire:model.defer="tingkat" class="w-full mt-1 border rounded px-3 py-2">
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>
                            </select>
                            @error('tingkat') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Wali Kelas</label>
                            <select wire:model.defer="wali_kelas_id" class="w-full mt-1 border rounded px-3 py-2">
                                <option value="">-- None --</option>
                                @foreach($guruList as $g)
                                    <option value="{{ $g['id'] }}">{{ $g['name'] }}</option>
                                @endforeach
                            </select>
                            @error('wali_kelas_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jurusan</label>
                        <input type="text" wire:model.defer="jurusan" class="w-full mt-1 border rounded px-3 py-2"/>
                        @error('jurusan') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancel
                    </button>
                    <button wire:click="save" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        {{ $editMode ? 'Update' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
