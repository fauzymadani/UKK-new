<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapAbsensi extends Model
{
    use HasFactory;

    protected $table = 'rekap_absensi';

    protected $fillable = [
        'siswa_id',
        'bulan',
        'total_hadir',
        'total_izin',
        'total_sakit',
        'total_alpha',
        'persentase_kehadiran',
    ];

    protected $casts = [
        'total_hadir' => 'integer',
        'total_izin' => 'integer',
        'total_sakit' => 'integer',
        'total_alpha' => 'integer',
        'persentase_kehadiran' => 'decimal:2',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Scopes
    public function scopeBySiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    public function scopeByBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    // Helper methods
    public function getTotalKehadiranAttribute()
    {
        return $this->total_hadir + $this->total_izin + $this->total_sakit + $this->total_alpha;
    }

    public static function hitungRekap($siswaId, $bulan)
    {
        $absensi = Absensi::where('siswa_id', $siswaId)
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->get();

        $totalHadir = $absensi->where('status', 'hadir')->count();
        $totalIzin = $absensi->where('status', 'izin')->count();
        $totalSakit = $absensi->where('status', 'sakit')->count();
        $totalAlpha = $absensi->where('status', 'alpha')->count();

        $totalKeseluruhan = $absensi->count();
        $persentase = $totalKeseluruhan > 0 ? ($totalHadir / $totalKeseluruhan) * 100 : 0;

        return self::updateOrCreate(
            ['siswa_id' => $siswaId, 'bulan' => $bulan],
            [
                'total_hadir' => $totalHadir,
                'total_izin' => $totalIzin,
                'total_sakit' => $totalSakit,
                'total_alpha' => $totalAlpha,
                'persentase_kehadiran' => round($persentase, 2),
            ]
        );
    }
}
