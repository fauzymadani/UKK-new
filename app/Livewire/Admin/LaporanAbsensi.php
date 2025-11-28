<?php

namespace App\Livewire\Admin;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Class LaporanAbsensi
 *
 * Admin component for viewing comprehensive attendance reports.
 *
 * @package App\Livewire\Admin
 */
class LaporanAbsensi extends Component
{
    use WithPagination;

    /**
     * @var int|null Class filter
     */
    public $kelas_id = '';

    /**
     * @var int|null Student filter
     */
    public $siswa_id = '';

    /**
     * @var string Start date
     */
    public $tanggal_mulai;

    /**
     * @var string End date
     */
    public $tanggal_selesai;

    /**
     * @var string Status filter
     */
    public $status_filter = '';

    /**
     * @var string Report type (detail or summary)
     */
    public $reportType = 'detail';

    /**
     * @var array Available classes
     */
    public $kelasList = [];

    /**
     * @var array Available students
     */
    public $siswaList = [];

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
    }

    /**
     * Handle class selection change.
     *
     * @return void
     */
    public function updatedKelasId(): void
    {
        if ($this->kelas_id) {
            $this->siswaList = Siswa::where('kelas_id', $this->kelas_id)
                ->with('user')
                ->orderBy('nis')
                ->get()
                ->map(fn($s) => ['id' => $s->id, 'name' => $s->user->name, 'nis' => $s->nis])
                ->toArray();
        } else {
            $this->siswaList = [];
        }

        $this->siswa_id = '';
        $this->resetPage();
    }

    /**
     * Reset filters.
     *
     * @return void
     */
    public function resetFilters(): void
    {
        $this->kelas_id = '';
        $this->siswa_id = '';
        $this->tanggal_mulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->format('Y-m-d');
        $this->status_filter = '';
        $this->siswaList = [];
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
     * Export report to Excel (placeholder).
     *
     * @return void
     */
    public function export(): void
    {
        // TODO: Implement Excel export
        session()->flash('info', 'Fitur export akan segera hadir!');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $query = Absensi::with(['siswa.user', 'siswa.kelas', 'jadwalPelajaran.mataPelajaran'])
            ->whereBetween('tanggal', [$this->tanggal_mulai, $this->tanggal_selesai]);

        if ($this->kelas_id) {
            $query->whereHas('siswa', function($q) {
                $q->where('kelas_id', $this->kelas_id);
            });
        }

        if ($this->siswa_id) {
            $query->where('siswa_id', $this->siswa_id);
        }

        if ($this->status_filter) {
            $query->where('status', $this->status_filter);
        }

        // Get all data for summary
        $allAbsensi = $query->get();
        $this->calculateSummary($allAbsensi);

        // Paginate for display
        $absensi = $query->orderBy('tanggal', 'desc')->paginate(20);

        return view('livewire.admin.laporan-absensi', [
            'absensi' => $absensi,
        ])->layout('layouts.admin');
    }

    /**
     * Reset pagination when filters change.
     *
     * @return void
     */
    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['siswa_id', 'tanggal_mulai', 'tanggal_selesai', 'status_filter'])) {
            $this->resetPage();
        }
    }
}
