<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'siswa_id',
        'jadwal_pelajaran_id',
        'tanggal',
        'status',
        'keterangan',
        'waktu_absen',
        'dicatat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_absen' => 'datetime:H:i',
    ];

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }

    public function pencatat()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // Scopes untuk query optimization
    public function scopeBySiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    public function scopeByBulan($query, $bulan, $tahun = null)
    {
        if ($tahun) {
            return $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
        }
        return $query->whereMonth('tanggal', $bulan);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeHadir($query)
    {
        return $query->where('status', 'hadir');
    }

    public function scopeAlpha($query)
    {
        return $query->where('status', 'alpha');
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['siswa.user', 'jadwalPelajaran.mataPelajaran', 'pencatat']);
    }

    // Helper methods
    public function isTerlambat($batasTerlambat = 15)
    {
        if ($this->status !== 'hadir' || !$this->waktu_absen) {
            return false;
        }

        $jamMulai = Carbon::parse($this->jadwalPelajaran->jam_mulai);
        $waktuAbsen = Carbon::parse($this->waktu_absen);

        return $waktuAbsen->diffInMinutes($jamMulai, false) < -$batasTerlambat;
    }
}
