<?php

namespace App\Livewire\Admin;

use App\Services\DashboardService;
use Livewire\Component;

/**
 * Class Dashboard
 *
 * Admin dashboard component that delegates data logic to DashboardService.
 */
class Dashboard extends Component
{
    public $stats = [
        'total_siswa' => 0,
        'total_guru' => 0,
        'total_kelas' => 0,
        'total_absensi_hari_ini' => 0,
    ];

    public $absensiHariIni = [
        'hadir' => 0,
        'izin' => 0,
        'sakit' => 0,
        'alpha' => 0,
    ];

    public $recentActivities = [];

    protected DashboardService $service;

    public function mount(): void
    {
        $this->service = app(DashboardService::class);
        $this->loadAll();
    }

    protected function loadAll(): void
    {
        $this->stats = $this->service->getStatistics();
        $this->absensiHariIni = $this->service->getTodayAttendance();
        $this->recentActivities = $this->service->getRecentActivities();
    }

    public function refresh(): void
    {
        $this->loadAll();
        session()->flash('success', 'Dashboard berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.admin');
    }
}
