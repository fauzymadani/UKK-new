<?php

namespace App\Livewire\Admin;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class ManageJadwal
 *
 * Admin component for managing class schedules.
 *
 * @package App\Livewire\Admin
 */
class ManageJadwal extends Component
{
    use WithPagination;

    /**
     * @var string Search query
     */
    public $search = '';

    /**
     * @var string Day filter
     */
    public $hariFilter = '';

    /**
     * @var bool Show create/edit modal
     */
    public $showModal = false;

    /**
     * @var bool Edit mode flag
     */
    public $editMode = false;

    /**
     * @var int|null Schedule ID being edited
     */
    public $jadwalId = null;

    /**
     * Form fields
     */
    public $kelas_id = '';
    public $mata_pelajaran_id = '';
    public $hari = 'Senin';
    public $jam_mulai = '';
    public $jam_selesai = '';

    /**
     * @var array Available classes
     */
    public $kelasList = [];

    /**
     * @var array Available subjects
     */
    public $mapelList = [];

    /**
     * @var array Days of the week
     */
    public $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    /**
     * Validation rules.
     *
     * @return array
     */
    protected function rules(): array
    {
        return [
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ];
    }

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->loadOptions();
    }

    /**
     * Load dropdown options.
     *
     * @return void
     */
    protected function loadOptions(): void
    {
        $this->kelasList = Kelas::orderBy('nama_kelas')->get()->toArray();
        $this->mapelList = MataPelajaran::with('guru')->orderBy('nama_mapel')->get()->toArray();
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
     * @param int $id Schedule ID
     * @return void
     */
    public function edit(int $id): void
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $this->jadwalId = $jadwal->id;
        $this->kelas_id = $jadwal->kelas_id;
        $this->mata_pelajaran_id = $jadwal->mata_pelajaran_id;
        $this->hari = $jadwal->hari;
        $this->jam_mulai = $jadwal->jam_mulai->format('H:i');
        $this->jam_selesai = $jadwal->jam_selesai->format('H:i');

        $this->editMode = true;
        $this->showModal = true;
    }

    /**
     * Save schedule (create or update).
     *
     * @return void
     */
    public function save(): void
    {
        $this->validate();

        try {
            $data = [
                'kelas_id' => $this->kelas_id,
                'mata_pelajaran_id' => $this->mata_pelajaran_id,
                'hari' => $this->hari,
                'jam_mulai' => $this->jam_mulai,
                'jam_selesai' => $this->jam_selesai,
            ];

            if ($this->editMode) {
                JadwalPelajaran::findOrFail($this->jadwalId)->update($data);
                session()->flash('success', 'Jadwal berhasil diupdate!');
            } else {
                JadwalPelajaran::create($data);
                session()->flash('success', 'Jadwal berhasil ditambahkan!');
            }

            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Delete schedule.
     *
     * @param int $id Schedule ID
     * @return void
     */
    public function delete(int $id): void
    {
        try {
            JadwalPelajaran::findOrFail($id)->delete();
            session()->flash('success', 'Jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
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
        $this->jadwalId = null;
        $this->kelas_id = '';
        $this->mata_pelajaran_id = '';
        $this->hari = 'Senin';
        $this->jam_mulai = '';
        $this->jam_selesai = '';
    }

    /**
     * Reset pagination when filters change.
     *
     * @return void
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when day filter changes.
     *
     * @return void
     */
    public function updatingHariFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render()
    {
        $query = JadwalPelajaran::with(['kelas', 'mataPelajaran.guru']);

        if ($this->search) {
            $query->whereHas('kelas', function($q) {
                $q->where('nama_kelas', 'like', '%' . $this->search . '%');
            })->orWhereHas('mataPelajaran', function($q) {
                $q->where('nama_mapel', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->hariFilter) {
            $query->where('hari', $this->hariFilter);
        }

        $jadwal = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(15);

        return view('livewire.admin.manage-jadwal', [
            'jadwal' => $jadwal,
        ])->layout('layouts.admin');
    }
}
