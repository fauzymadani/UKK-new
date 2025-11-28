<?php

namespace App\Livewire\Guru;

use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

/**
 * Class JadwalMengajar
 *
 * Livewire component for displaying teacher's teaching schedule.
 * Shows weekly schedule organized by day.
 *
 * @package App\Livewire\Guru
 */
class JadwalMengajar extends Component
{
    /**
     * @var array Weekly schedule grouped by day
     */
    public $jadwalPerHari = [];

    /**
     * @var array Days of the week
     */
    public $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->loadJadwal();
    }

    /**
     * Load teaching schedule.
     *
     * @return void
     */
    protected function loadJadwal(): void
    {
        $jadwal = JadwalPelajaran::query()
            ->whereHas('mataPelajaran', function ($query) {
                $query->where('guru_id', Auth::id());
            })
            ->with(['kelas', 'mataPelajaran'])
            ->orderBy('jam_mulai')
            ->get();

        $this->jadwalPerHari = [];

        foreach ($this->hariList as $hari) {
            $this->jadwalPerHari[$hari] = $jadwal
                ->where('hari', $hari)
                ->values()
                ->toArray();
        }
    }

    /**
     * Render the component.
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.guru.jadwal-mengajar')
            ->layout('layouts.guru');
    }
}
