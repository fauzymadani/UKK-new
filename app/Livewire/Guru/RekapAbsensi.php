<?php

namespace App\Livewire\Guru;

use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class RekapAbsensi
 *
 * Livewire component for viewing attendance summary.
 * Displays attendance records with filtering capabilities.
 *
 * @package App\Livewire\Guru
 */
class RekapAbsensi extends Component
{
    use WithPagination;

    /**
     * @var int Selected schedule ID for filtering
     */
    public $jadwal_id = '';

    /**
     * @var string Start date for filtering
     */
    public $tanggal_mulai;

    /**
     * @var string End date for filtering
     */
    public $tanggal_selesai;

    /**
     * @var string Status filter
     */
    public $status_filter = '';

    /**
     * @var array Available schedules
     */
    public $jadwalOptions = [];

    /**
     * @var array Summary statistics
     */
    public $summary = [];

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->tanggal_mulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->format('Y-m-d');

        $this->loadJadwalOptions();
    }

    /**
     * Load available teaching schedules.
     *
     * @return void
     */
    protected function loadJadwalOptions(): void
    {
        $this->jadwalOptions = JadwalPelajaran::query()
            ->whereHas('mataPelajaran', function ($query) {
                $query->where('guru_id', Auth::id());
            })
            ->with(['kelas', 'mataPelajaran'])
            ->get()
            ->map(function ($jadwal) {
                return [
                    'id' => $jadwal->id,
                    'label' => "{$jadwal->kelas->nama_kelas} - {$jadwal->mataPelajaran->nama_mapel}",
                ];
            })
            ->toArray();
    }

    /**
     * Reset filters.
     *
     * @return void
     */
    public function resetFilters(): void
    {
        $this->jadwal_id = '';
        $this->tanggal_mulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->format('Y-m-d');
        $this->status_filter = '';
        $this->resetPage();
    }

    /**
     * Calculate summary statistics.
     *
     * @param \Illuminate\Database\Eloquent\Collection $absensi
     * @return void
     */
    protected function calculateSummary($absensi): void
    {
        $this->summary = [
            'total' => $absensi->count(),
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'izin' => $absensi->where('status', 'izin')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'alpha' => $absensi->where('status', 'alpha')->count(),
        ];

        if ($this->summary['total'] > 0) {
            $this->summary['persentase_hadir'] = round(
                ($this->summary['hadir'] / $this->summary['total']) * 100,
                2
            );
        } else {
            $this->summary['persentase_hadir'] = 0;
        }
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $query = Absensi::query()
            ->whereHas('jadwalPelajaran.mataPelajaran', function ($q) {
                $q->where('guru_id', Auth::id());
            })
            ->with(['siswa.user', 'jadwalPelajaran.kelas', 'jadwalPelajaran.mataPelajaran'])
            ->whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_selesai])
            ->orderBy('tanggal', 'desc');

        if ($this->jadwal_id) {
            $query->where('jadwal_pelajaran_id', $this->jadwal_id);
        }

        if ($this->status_filter) {
            $query->where('status', $this->status_filter);
        }

        // Get all data for summary (without pagination)
        $allAbsensi = $query->get();
        $this->calculateSummary($allAbsensi);

        // Paginate for display
        $absensi = $query->paginate(15);

        return view('livewire.guru.rekap-absensi', [
            'absensi' => $absensi,
        ])->layout('layouts.guru');
    }

    /**
     * Reset pagination when filters change.
     *
     * @return void
     */
    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['jadwal_id', 'tanggal_mulai', 'tanggal_selesai', 'status_filter'])) {
            $this->resetPage();
        }
    }
}
