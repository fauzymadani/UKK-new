<div>
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
                placeholder="Search code or name..."
                class="border rounded px-3 py-2 w-72"
            />
            <button wire:click="create" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                + Add Subject
            </button>
        </div>

        <div>
            <span class="text-sm text-gray-600">Showing {{ $mapel->firstItem() ?? 0 }} - {{ $mapel->lastItem() ?? 0 }} of {{ $mapel->total() }}</span>
        </div>
    </div>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Code</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Name</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Teacher</th>
                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">Actions</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
            @forelse($mapel as $item)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->kode_mapel }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->nama_mapel }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ $item->guru->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-right space-x-2">
                        <button wire:click="edit({{ $item->id }})"
                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit
                        </button>
                        <button
                            wire:click="delete({{ $item->id }})"
                            onclick="confirm('Delete this subject?') || event.stopImmediatePropagation()"
                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                        >
                            Delete
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-600">No subjects found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $mapel->links() }}
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-40 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg w-full max-w-xl shadow-lg">
                <div class="px-6 py-4 border-b flex items-center justify-between">
                    <h3 class="text-lg font-semibold">{{ $editMode ? 'Edit Mata Pelajaran' : 'Create Mata Pelajaran' }}</h3>
                    <button wire:click="closeModal" class="text-gray-600 hover:text-gray-800">&times;</button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Code</label>
                        <input type="text" wire:model.defer="kode_mapel" class="w-full mt-1 border rounded px-3 py-2"/>
                        @error('kode_mapel') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" wire:model.defer="nama_mapel" class="w-full mt-1 border rounded px-3 py-2"/>
                        @error('nama_mapel') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teacher</label>
                        <select wire:model.defer="guru_id" class="w-full mt-1 border rounded px-3 py-2">
                            <option value="">-- Select Teacher --</option>
                            @foreach($guruList as $g)
                                <option value="{{ $g['id'] }}">{{ $g['name'] }}</option>
                            @endforeach
                        </select>
                        @error('guru_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
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
