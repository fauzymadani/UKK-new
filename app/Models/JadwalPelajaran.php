<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajaran';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    // Relationships
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'jadwal_pelajaran_id');
    }

    // Scopes untuk query optimization
    public function scopeByKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }

    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    public function scopeHariIni($query)
    {
        $hari = Carbon::now()->locale('id')->isoFormat('dddd');
        return $query->where('hari', $hari);
    }

    public function scopeWithRelations($query)
    {
        return $query->with(['kelas', 'mataPelajaran.guru']);
    }

    // Helper methods
    public function isActiveNow()
    {
        $now = Carbon::now();
        $hariSekarang = $now->locale('id')->isoFormat('dddd');

        if ($this->hari !== $hariSekarang) {
            return false;
        }

        $jamSekarang = $now->format('H:i:s');
        return $jamSekarang >= $this->jam_mulai && $jamSekarang <= $this->jam_selesai;
    }
}
