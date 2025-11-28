<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AbsensiResource
 *
 * API resource for transforming Absensi (Attendance) model into JSON response.
 * Provides comprehensive attendance data with related information.
 *
 * @package App\Http\Resources
 */
class AbsensiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request
     * @return array<string, mixed> Transformed attendance data
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'siswa_id' => $this->siswa_id,
            'jadwal_pelajaran_id' => $this->jadwal_pelajaran_id,
            'tanggal' => $this->tanggal?->format('Y-m-d'),
            'tanggal_formatted' => $this->tanggal?->locale('id')->isoFormat('dddd, D MMMM YYYY'),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'keterangan' => $this->keterangan,
            'waktu_absen' => $this->waktu_absen?->format('H:i:s'),
            'waktu_absen_formatted' => $this->waktu_absen?->format('H:i'),
            'dicatat_oleh' => $this->dicatat_oleh,

            // Student relationship
            'siswa' => $this->when(
                $this->relationLoaded('siswa'),
                fn() => new SiswaResource($this->siswa)
            ),

            // Schedule relationship
            'jadwal_pelajaran' => $this->when(
                $this->relationLoaded('jadwalPelajaran'),
                fn() => new JadwalPelajaranResource($this->jadwalPelajaran)
            ),

            // Recorded by relationship
            'pencatat' => $this->when(
                $this->relationLoaded('pencatat'),
                fn() => new UserResource($this->pencatat)
            ),

            // Late status
            'is_terlambat' => $this->when(
                method_exists($this, 'isTerlambat'),
                fn() => $this->isTerlambat()
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get status label in Indonesian.
     *
     * @return string Status label
     */
    private function getStatusLabel(): string
    {
        return match($this->status) {
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha',
            default => 'Unknown',
        };
    }

    /**
     * Get status color for UI.
     *
     * @return string Color code or class name
     */
    private function getStatusColor(): string
    {
        return match($this->status) {
            'hadir' => 'success',
            'izin' => 'warning',
            'sakit' => 'info',
            'alpha' => 'danger',
            default => 'secondary',
        };
    }
}
