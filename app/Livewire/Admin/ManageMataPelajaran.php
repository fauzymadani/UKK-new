<?php

namespace App\Livewire\Admin;

use App\Models\MataPelajaran;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class ManageMataPelajaran
 *
 * Admin component for managing subjects/courses.
 *
 * @package App\Livewire\Admin
 */
class ManageMataPelajaran extends Component
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
     * @var int|null Subject ID being edited
     */
    public $mapelId = null;

    /**
     * Form fields
     */
    public $kode_mapel = '';
    public $nama_mapel = '';
    public $guru_id = '';

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
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel,' . $this->mapelId,
            'nama_mapel' => 'required|string|max:100',
            'guru_id' => 'required|exists:users,id',
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
     * @param int $id Subject ID
     * @return void
     */
    public function edit(int $id): void
    {
        $mapel = MataPelajaran::findOrFail($id);

        $this->mapelId = $mapel->id;
        $this->kode_mapel = $mapel->kode_mapel;
        $this->nama_mapel = $mapel->nama_mapel;
        $this->guru_id = $mapel->guru_id;

        $this->editMode = true;
        $this->showModal = true;
    }

    /**
     * Save subject (create or update).
     *
     * @return void
     */
    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'kode_mapel' => $this->kode_mapel,
                'nama_mapel' => $this->nama_mapel,
                'guru_id' => $this->guru_id,
            ];

            if ($this->editMode) {
                MataPelajaran::findOrFail($this->mapelId)->update($data);
                session()->flash('success', 'Mata pelajaran berhasil diupdate!');
            } else {
                MataPelajaran::create($data);
                session()->flash('success', 'Mata pelajaran berhasil ditambahkan!');
            }

            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan mata pelajaran: ' . $e->getMessage());
        }
    }

    /**
     * Delete subject.
     *
     * @param int $id Subject ID
     * @return void
     */
    public function delete(int $id): void
    {
        try {
            MataPelajaran::findOrFail($id)->delete();
            session()->flash('success', 'Mata pelajaran berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus mata pelajaran: ' . $e->getMessage());
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
        $this->mapelId = null;
        $this->kode_mapel = '';
        $this->nama_mapel = '';
        $this->guru_id = '';
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
        $query = MataPelajaran::with('guru');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('kode_mapel', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_mapel', 'like', '%' . $this->search . '%');
            });
        }

        $mapel = $query->orderBy('nama_mapel')->paginate(15);

        return view('livewire.admin.manage-mata-pelajaran', [
            'mapel' => $mapel,
        ])->layout('layouts.admin');
    }
}
