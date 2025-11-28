<?php
namespace App\Livewire\Admin;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class LaporanAbsensi extends Component
{
    use WithPagination;

    public $kelas_id = '';
    public $siswa_id = '';
    public $tanggal_mulai;
    public $tanggal_selesai;
    public $status_filter = '';
    public $reportType = 'detail';

    public $kelasList = [];
    public $siswaList = [];
    public $summary = [];

    // Make service nullable to avoid "accessed before initialization" during Livewire lifecycle
    protected ?AbsensiService $service = null;

    public function mount(): void
    {
        $this->tanggal_mulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->format('Y-m-d');

        // optional eager resolution
        $this->service = app(AbsensiService::class);
        $this->loadOptions();
        $this->refreshSummary();
    }

    /**
     * Lazily resolve the service to ensure it's always available when used.
     */
    protected function service(): AbsensiService
    {
        return $this->service ??= app(AbsensiService::class);
    }

    protected function loadOptions(): void
    {
        $this->kelasList = Kelas::orderBy('nama_kelas')->get()->toArray();
    }

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
        $this->refreshSummary();
    }

    public function resetFilters(): void
    {
        $this->kelas_id = '';
        $this->siswa_id = '';
        $this->tanggal_mulai = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->tanggal_selesai = Carbon::now()->format('Y-m-d');
        $this->status_filter = '';
        $this->siswaList = [];
        $this->resetPage();
        $this->refreshSummary();
    }

    protected function filters(): array
    {
        return [
            'tanggal_mulai' => $this->tanggal_mulai,
            'tanggal_selesai' => $this->tanggal_selesai,
            'kelas_id' => $this->kelas_id,
            'siswa_id' => $this->siswa_id,
            'status' => $this->status_filter,
        ];
    }

    protected function refreshSummary(): void
    {
        $this->summary = $this->service()->getSummary($this->filters());
    }

    public function export(): void
    {
        $result = $this->service()->export($this->filters());
        session()->flash('info', 'Export started or not implemented yet.');
    }

    public function render(): View
    {
        $this->refreshSummary();

        $absensi = $this->service()->paginate($this->filters(), 20);

        return view('livewire.admin.laporan-absensi', [
            'absensi' => $absensi,
        ])->layout('layouts.admin');
    }

    public function updated(string $propertyName): void
    {
        if (in_array($propertyName, ['siswa_id', 'tanggal_mulai', 'tanggal_selesai', 'status_filter'])) {
            $this->resetPage();
            $this->refreshSummary();
        }
    }
}
