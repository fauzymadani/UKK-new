<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class JadwalPelajaranResource
 *
 * API resource for transforming JadwalPelajaran (Schedule) model into JSON response.
 * Provides schedule information with class and subject details.
 *
 * @package App\Http\Resources
 */
class JadwalPelajaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request
     * @return array<string, mixed> Transformed schedule data
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kelas_id' => $this->kelas_id,
            'mata_pelajaran_id' => $this->mata_pelajaran_id,
            'hari' => $this->hari,
            'jam_mulai' => $this->jam_mulai?->format('H:i'),
            'jam_selesai' => $this->jam_selesai?->format('H:i'),

            // Class relationship
            'kelas' => $this->when(
                $this->relationLoaded('kelas'),
                fn() => new KelasResource($this->kelas)
            ),

            // Subject relationship
            'mata_pelajaran' => $this->when(
                $this->relationLoaded('mataPelajaran'),
                fn() => new MataPelajaranResource($this->mataPelajaran)
            ),

            // Additional info
            'is_active_now' => $this->when(
                method_exists($this, 'isActiveNow'),
                fn() => $this->isActiveNow()
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
