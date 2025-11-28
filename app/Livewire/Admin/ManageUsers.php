<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Validation\Rule;

/**
 * Class ManageUsers
 *
 * Admin component for managing all users (Admin, Guru, Siswa).
 * OPTIMIZED VERSION - Fixed performance issues
 *
 * @package App\Livewire\Admin
 */
class ManageUsers extends Component
{
    use WithPagination;

    /**
     * @var string Search query
     */
    public $search = '';

    /**
     * @var string Role filter
     */
    public $roleFilter = '';

    /**
     * @var bool Show create/edit modal
     */
    public $showModal = false;

    /**
     * @var bool Edit mode flag
     */
    public $editMode = false;

    /**
     * @var int|null User ID being edited
     */
    public $userId = null;

    /**
     * Form fields
     */
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'siswa';

    // Siswa specific fields
    public $nis = '';
    public $nisn = '';
    public $kelas_id = '';
    public $jenis_kelamin = 'L';
    public $alamat = '';
    public $no_telp = '';

    /**
     * OPTIMIZED: Computed property untuk kelas list
     * Hanya load saat dibutuhkan (modal dibuka)
     */
    #[Computed]
    public function kelasList()
    {
        return Kelas::orderBy('nama_kelas')->get();
    }

    /**
     * Validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        // find siswa id (if editing) so unique rules ignore the correct record
        $siswaId = null;
        if ($this->editMode && $this->userId) {
            $siswa = Siswa::where('user_id', $this->userId)->first();
            $siswaId = $siswa?->id;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required|in:admin,guru,siswa',
        ];

        if (!$this->editMode || $this->password) {
            $rules['password'] = 'required|min:8|confirmed';
        }

        if ($this->role === 'siswa') {
            $rules['nis'] = ['required', 'string', Rule::unique('siswa', 'nis')->ignore($siswaId)];
            $rules['nisn'] = ['required', 'string', Rule::unique('siswa', 'nisn')->ignore($siswaId)];
            $rules['kelas_id'] = 'required|exists:kelas,id';
            $rules['jenis_kelamin'] = 'required|in:L,P';
        }

        return $rules;
    }

    /**
     * Open create modal.
     *
     * @return void
     */
    public function create(): void
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    /**
     * Open edit modal.
     *
     * @param int $id User ID
     * @return void
     */
    public function edit(int $id): void
    {
        $user = User::with('siswa')->findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;

        if ($user->siswa) {
            $this->nis = $user->siswa->nis;
            $this->nisn = $user->siswa->nisn;
            $this->kelas_id = $user->siswa->kelas_id;
            $this->jenis_kelamin = $user->siswa->jenis_kelamin;
            $this->alamat = $user->siswa->alamat ?? '';
            $this->no_telp = $user->siswa->no_telp ?? '';
        }

        $this->editMode = true;
        $this->showModal = true;
    }

    /**
     * Save user (create or update).
     *
     * @return void
     */
    public function save(): void
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
            ];

            if ($this->password) {
                $userData['password'] = Hash::make($this->password);
            }

            if ($this->editMode) {
                $user = User::findOrFail($this->userId);
                $user->update($userData);
            } else {
                $user = User::create($userData);
            }

            // Handle siswa specific data
            if ($this->role === 'siswa') {
                $siswaData = [
                    'user_id' => $user->id,
                    'nis' => $this->nis,
                    'nisn' => $this->nisn,
                    'kelas_id' => $this->kelas_id,
                    'jenis_kelamin' => $this->jenis_kelamin,
                    'alamat' => $this->alamat,
                    'no_telp' => $this->no_telp,
                ];

                if ($this->editMode && $user->siswa) {
                    $user->siswa->update($siswaData);
                } else {
                    Siswa::create($siswaData);
                }
            }

            DB::commit();

            session()->flash('success', $this->editMode ? 'User berhasil diupdate!' : 'User berhasil ditambahkan!');
            $this->closeModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyimpan user: ' . $e->getMessage());
        }
    }

    /**
     * Delete user.
     *
     * @param int $id User ID
     * @return void
     */
    public function delete(int $id): void
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            session()->flash('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Close modal and reset form.
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Reset form fields.
     *
     * @return void
     */
    protected function resetForm(): void
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'siswa';
        $this->nis = '';
        $this->nisn = '';
        $this->kelas_id = '';
        $this->jenis_kelamin = 'L';
        $this->alamat = '';
        $this->no_telp = '';
    }

    /**
     * Reset pagination when search changes.
     *
     * @return void
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * OPTIMIZED: Render with eager loading
     *
     * @return View
     */
    public function render(): View
    {
        $query = User::query();

        // OPTIMIZED: Eager load siswa relationship to prevent N+1
        $query->with('siswa.kelas');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.admin.manage-users', [
            'users' => $users,
        ])->layout('layouts.admin');
    }
}
