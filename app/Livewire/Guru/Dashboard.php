<?php

namespace App\Livewire\Guru;

use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Class Dashboard
 *
 * Livewire component for teacher dashboard.
 * Displays today's schedule and attendance summary.
 *
 * @package App\Livewire\Guru
 */
class Dashboard extends Component
{
    /**
     * @var array Today's schedule
     */
    public $jadwalHariIni = [];

    /**
     * @var array Attendance statistics
     */
    public $statistik = [
        'total_hadir' => 0,
        'total_izin' => 0,
        'total_sakit' => 0,
        'total_alpha' => 0,
    ];

    /**
     * Mount the component.
     * Load today's schedule and statistics.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->loadJadwalHariIni();
        $this->loadStatistik();
    }

    /**
     * Load today's teaching schedule.
     *
     * @return void
     */
    protected function loadJadwalHariIni(): void
    {
        $hari = Carbon::now()->locale('id')->dayName;

        $this->jadwalHariIni = \App\Models\JadwalPelajaran::query()
            ->whereHas('mataPelajaran', function ($query) {
                $query->where('guru_id', Auth::id());
            })
            ->where('hari', $hari)
            ->with(['kelas', 'mataPelajaran'])
            ->orderBy('jam_mulai')
            ->get()
            ->toArray();
    }

    /**
     * Load today's attendance statistics.
     *
     * @return void
     */
    protected function loadStatistik(): void
    {
        $today = Carbon::now()->format('Y-m-d');

        $absensi = \App\Models\Absensi::query()
            ->whereDate('tanggal', $today)
            ->whereHas('jadwalPelajaran.mataPelajaran', function ($query) {
                $query->where('guru_id', Auth::id());
            })
            ->get();

        $this->statistik = [
            'total_hadir' => $absensi->where('status', 'hadir')->count(),
            'total_izin' => $absensi->where('status', 'izin')->count(),
            'total_sakit' => $absensi->where('status', 'sakit')->count(),
            'total_alpha' => $absensi->where('status', 'alpha')->count(),
        ];
    }

    /**
     * Refresh dashboard data.
     *
     * @return void
     */
    public function refresh(): void
    {
        $this->loadJadwalHariIni();
        $this->loadStatistik();

        session()->flash('success', 'Dashboard berhasil diperbarui!');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.guru.dashboard')
            ->layout('layouts.guru');
    }
}
