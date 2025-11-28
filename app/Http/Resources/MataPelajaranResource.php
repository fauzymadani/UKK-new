<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class MataPelajaranResource
 *
 * API resource for transforming MataPelajaran (Subject) model into JSON response.
 * Provides structured subject data with teacher information.
 *
 * @package App\Http\Resources
 */
class MataPelajaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request
     * @return array<string, mixed> Transformed subject data
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_mapel' => $this->kode_mapel,
            'nama_mapel' => $this->nama_mapel,
            'guru_id' => $this->guru_id,

            // Teacher relationship
            'guru' => $this->when(
                $this->relationLoaded('guru'),
                fn() => new UserResource($this->guru)
            ),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
