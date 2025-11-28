<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SiswaResource
 *
 * API resource for transforming Siswa (Student) model into JSON response.
 * Provides comprehensive student data with related information.
 *
 * @package App\Http\Resources
 */
class SiswaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request
     * @return array<string, mixed> Transformed student data
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'nis' => $this->nis,
            'nisn' => $this->nisn,
            'kelas_id' => $this->kelas_id,
            'jenis_kelamin' => $this->jenis_kelamin,
            'jenis_kelamin_label' => $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            'alamat' => $this->alamat,
            'no_telp' => $this->no_telp,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,

            // User relationship
            'user' => $this->when(
                $this->relationLoaded('user'),
                fn() => new UserResource($this->user)
            ),

            // Class relationship
            'kelas' => $this->when(
                $this->relationLoaded('kelas'),
                fn() => new KelasResource($this->kelas)
            ),

            // Attendance statistics
            'persentase_kehadiran' => $this->when(
                $this->relationLoaded('absensi'),
                fn() => $this->persentase_kehadiran
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
