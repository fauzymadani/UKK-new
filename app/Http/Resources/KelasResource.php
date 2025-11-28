<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class KelasResource
 *
 * API resource for transforming Kelas (Class) model into JSON response.
 * Provides structured class data with optional relationships.
 *
 * @package App\Http\Resources
 */
class KelasResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request
     * @return array<string, mixed> Transformed class data
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_kelas' => $this->nama_kelas,
            'tingkat' => $this->tingkat,
            'jurusan' => $this->jurusan,
            'wali_kelas_id' => $this->wali_kelas_id,

            // Conditional relationships
            'wali_kelas' => $this->when(
                $this->relationLoaded('waliKelas'),
                fn() => new UserResource($this->waliKelas)
            ),

            'jumlah_siswa' => $this->when(
                $this->relationLoaded('siswa'),
                fn() => $this->siswa->count()
            ),

            'siswa' => $this->when(
                $this->relationLoaded('siswa'),
                fn() => SiswaResource::collection($this->siswa)
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
