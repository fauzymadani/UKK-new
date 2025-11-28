<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function getStatistics(): array
    {
        return [
            'total_siswa' => Siswa::count(),
            'total_guru' => User::where('role', 'guru')->count(),
            'total_kelas' => Kelas::count(),
            'total_absensi_hari_ini' => Absensi::whereDate('tanggal', Carbon::today())->count(),
        ];
    }

    public function getTodayAttendance(): array
    {
        $today = Carbon::today();
        $absensi = Absensi::whereDate('tanggal', $today)->get();

        return [
            'hadir' => $absensi->where('status', 'hadir')->count(),
            'izin' => $absensi->where('status', 'izin')->count(),
            'sakit' => $absensi->where('status', 'sakit')->count(),
            'alpha' => $absensi->where('status', 'alpha')->count(),
        ];
    }

    public function getRecentActivities(int $limit = 10): array
    {
        return Absensi::with(['siswa.user', 'pencatat'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($absensi) {
                return [
                    'siswa' => optional($absensi->siswa->user)->name ?? 'Unknown',
                    'status' => $absensi->status,
                    'pencatat' => optional($absensi->pencatat)->name ?? 'System',
                    'waktu' => optional($absensi->created_at)->diffForHumans() ?? '',
                ];
            })
            ->toArray();
    }
}
