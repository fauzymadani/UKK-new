<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'kelas_id',
        'jenis_kelamin',
        'alamat',
        'no_telp',
        'foto',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }

    public function rekapAbsensi()
    {
        return $this->hasMany(RekapAbsensi::class, 'siswa_id');
    }

    // Scopes
    public function scopeByKelas($query, $kelasId)
    {
        return $query->where('kelas_id', $kelasId);
    }

    public function scopeByNis($query, $nis)
    {
        return $query->where('nis', $nis);
    }

    // Eager loading untuk optimasi
    public function scopeWithRelations($query)
    {
        return $query->with(['user', 'kelas.waliKelas']);
    }

    // Helper methods
    public function getPersentaseKehadiranAttribute()
    {
        $totalAbsensi = $this->absensi()->count();
        if ($totalAbsensi === 0) return 0;

        $totalHadir = $this->absensi()->where('status', 'hadir')->count();
        return round(($totalHadir / $totalAbsensi) * 100, 2);
    }
}
