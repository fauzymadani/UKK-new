{{-- FILE: resources/views/livewire/admin/manage-users.blade.php --}}
<div>
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Kelola Users</h2>
        <button wire:click="create"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Tambah User
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" wire:model.live="search" placeholder="Cari nama atau email..."
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <select wire:model.live="roleFilter"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="guru">Guru</option>
                    <option value="siswa">Siswa</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dibuat</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        @if($user->siswa)
                            <div class="text-xs text-gray-500">NIS: {{ $user->siswa->nis }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $roleColors = [
                                'admin' => 'bg-purple-100 text-purple-800',
                                'guru' => 'bg-blue-100 text-blue-800',
                                'siswa' => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] }}">
                                {{ ucfirst($user->role) }}
                            </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $user->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            Edit
                        </button>
                        <button wire:click="delete({{ $user->id }})"
                                onclick="return confirm('Yakin ingin menghapus user ini?')"
                                class="text-red-600 hover:text-red-900">
                            Hapus
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-600">
                        Tidak ada data user
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 bg-gray-50">
            {{ $users->links() }}
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $editMode ? 'Edit User' : 'Tambah User' }}
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
                        {{-- Basic Info --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" wire:model="name"
                                       class="w-full border-gray-300 rounded-lg shadow-sm">
                                @error('name') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" wire:model="email"
                                       class="w-full border-gray-300 rounded-lg shadow-sm">
                                @error('email') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input type="password" wire:model="password"
                                       class="w-full border-gray-300 rounded-lg shadow-sm"
                                       placeholder="{{ $editMode ? 'Kosongkan jika tidak ingin mengubah' : '' }}">
                                @error('password') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                                <input type="password" wire:model="password_confirmation"
                                       class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select wire:model.live="role" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="siswa">Siswa</option>
                                <option value="guru">Guru</option>
                                <option value="admin">Admin</option>
                            </select>
                            @error('role') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Siswa Specific Fields --}}
                        @if($role === 'siswa')
                            <div class="border-t pt-4 mt-4">
                                <h4 class="font-medium text-gray-900 mb-4">Data Siswa</h4>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">NIS</label>
                                        <input type="text" wire:model="nis"
                                               class="w-full border-gray-300 rounded-lg shadow-sm">
                                        @error('nis') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">NISN</label>
                                        <input type="text" wire:model="nisn"
                                               class="w-full border-gray-300 rounded-lg shadow-sm">
                                        @error('nisn') <span
                                            class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                                        <select wire:model="kelas_id"
                                                class="w-full border-gray-300 rounded-lg shadow-sm">
                                            <option value="">Pilih Kelas</option>
                                            @foreach($kelasList as $kelas)
                                                <option value="{{ $kelas['id'] }}">{{ $kelas['nama_kelas'] }}</option>
                                            @endforeach
                                        </select>
                                        @error('kelas_id') <span
                                            class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis
                                            Kelamin</label>
                                        <select wire:model="jenis_kelamin"
                                                class="w-full border-gray-300 rounded-lg shadow-sm">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                        @error('jenis_kelamin') <span
                                            class="text-red-600 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                                    <textarea wire:model="alamat" rows="2"
                                              class="w-full border-gray-300 rounded-lg shadow-sm"></textarea>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                                    <input type="text" wire:model="no_telp"
                                           class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>
                            </div>
                        @endif
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
