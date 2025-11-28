<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class ManageKelas
 *
 * Admin component for managing classes.
 *
 * @package App\Livewire\Admin
 */
class ManageKelas extends Component
{
    use WithPagination;

    /**
     * @var string Search query
     */
    public $search = '';

    /**
     * @var bool Show create/edit modal
     */
    public $showModal = false;

    /**
     * @var bool Edit mode flag
     */
    public $editMode = false;

    /**
     * @var int|null Class ID being edited
     */
    public $kelasId = null;

    /**
     * Form fields
     */
    public $nama_kelas = '';
    public $tingkat = 'X';
    public $jurusan = '';
    public $wali_kelas_id = '';

    /**
     * @var array Available teachers
     */
    public $guruList = [];

    /**
     * Validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,' . $this->kelasId,
            'tingkat' => 'required|in:X,XI,XII',
            'jurusan' => 'required|string|max:50',
            'wali_kelas_id' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->loadGuruList();
    }

    /**
     * Load available teachers.
     *
     * @return void
     */
    protected function loadGuruList(): void
    {
        $this->guruList = User::where('role', 'guru')
            ->orderBy('name')
            ->get()
            ->toArray();
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
     * @param int $id Class ID
     * @return void
     */
    public function edit(int $id): void
    {
        $kelas = Kelas::findOrFail($id);

        $this->kelasId = $kelas->id;
        $this->nama_kelas = $kelas->nama_kelas;
        $this->tingkat = $kelas->tingkat;
        $this->jurusan = $kelas->jurusan;
        $this->wali_kelas_id = $kelas->wali_kelas_id;

        $this->editMode = true;
        $this->showModal = true;
    }

    /**
     * Save class (create or update).
     *
     * @return void
     */
    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'nama_kelas' => $this->nama_kelas,
                'tingkat' => $this->tingkat,
                'jurusan' => $this->jurusan,
                'wali_kelas_id' => $this->wali_kelas_id ?: null,
            ];

            if ($this->editMode) {
                Kelas::findOrFail($this->kelasId)->update($data);
                session()->flash('success', 'Kelas berhasil diupdate!');
            } else {
                Kelas::create($data);
                session()->flash('success', 'Kelas berhasil ditambahkan!');
            }

            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Delete class.
     *
     * @param int $id Class ID
     * @return void
     */
    public function delete(int $id): void
    {
        try {
            Kelas::findOrFail($id)->delete();
            session()->flash('success', 'Kelas berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus kelas: ' . $e->getMessage());
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
        $this->kelasId = null;
        $this->nama_kelas = '';
        $this->tingkat = 'X';
        $this->jurusan = '';
        $this->wali_kelas_id = '';
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
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $query = Kelas::with('waliKelas');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_kelas', 'like', '%' . $this->search . '%')
                    ->orWhere('jurusan', 'like', '%' . $this->search . '%');
            });
        }

        $kelas = $query->orderBy('tingkat')->orderBy('nama_kelas')->paginate(15);

        return view('livewire.admin.manage-kelas', [
            'kelas' => $kelas,
        ])->layout('layouts.admin');
    }
}
