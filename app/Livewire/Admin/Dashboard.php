<?php

namespace App\Livewire\Admin;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

/**
 * Class Dashboard
 *
 * Admin dashboard displaying system overview and statistics.
 *
 * @package App\Livewire\Admin
 */
class Dashboard extends Component
{
    /**
     * @var array System statistics
     */
    public $stats = [
        'total_siswa' => 0,
        'total_guru' => 0,
        'total_kelas' => 0,
        'total_absensi_hari_ini' => 0,
    ];

    /**
     * @var array Today's attendance breakdown
     */
    public $absensiHariIni = [
        'hadir' => 0,
        'izin' => 0,
        'sakit' => 0,
        'alpha' => 0,
    ];

    /**
     * @var array Recent activities
     */
    public $recentActivities = [];

    /**
     * Mount the component.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->loadStatistics();
        $this->loadTodayAttendance();
        $this->loadRecentActivities();
    }

    /**
     * Load system statistics.
     *
     * @return void
     */
    protected function loadStatistics(): void
    {
        $this->stats = [
            'total_siswa' => Siswa::count(),
            'total_guru' => User::where('role', 'guru')->count(),
            'total_kelas' => Kelas::count(),
            'total_absensi_hari_ini' => Absensi::whereDate('tanggal', Carbon::today())->count(),
        ];
    }

    /**
     * Load today's attendance statistics.
     *
     * @return void
     */
    protected function loadTodayAttendance(): void
    {
        $today = Carbon::today();
        $absensi = Absensi::whereDate('tanggal', $today)->get();

        $this->absensiHariIni = [
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'izin' => $absensi->where('status', 'izin')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'alpha' => $absensi->where('status', 'alpha')->count(),
        ];
    }

    /**
     * Load recent activities.
     *
     * @return void
     */
    protected function loadRecentActivities(): void
    {
        $this->recentActivities = Absensi::with(['siswa.user', 'pencatat'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($absensi) {
                return [
                    'siswa' => $absensi->siswa->user->name,
                    'status' => $absensi->status,
                    'pencatat' => $absensi->pencatat->name,
                    'waktu' => $absensi->created_at->diffForHumans(),
                ];
            })
            ->toArray();
    }

    /**
     * Refresh dashboard data.
     *
     * @return void
     */
    public function refresh(): void
    {
        $this->loadStatistics();
        $this->loadTodayAttendance();
        $this->loadRecentActivities();

        session()->flash('success', 'Dashboard berhasil diperbarui!');
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin');
    }
}
