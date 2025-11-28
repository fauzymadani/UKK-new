<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class RekapAbsensiResource
 *
 * API resource for transforming RekapAbsensi (Attendance Summary) model into JSON response.
 * Provides monthly attendance statistics and summaries.
 *
 * @package App\Http\Resources
 */
class RekapAbsensiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request The incoming HTTP request
     * @return array<string, mixed> Transformed attendance summary data
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'siswa_id' => $this->siswa_id,
            'bulan' => $this->bulan,
            'bulan_formatted' => $this->getBulanFormatted(),
            'total_hadir' => $this->total_hadir,
            'total_izin' => $this->total_izin,
            'total_sakit' => $this->total_sakit,
            'total_alpha' => $this->total_alpha,
            'total_kehadiran' => $this->total_kehadiran,
            'persentase_kehadiran' => (float) $this->persentase_kehadiran,
            'persentase_formatted' => number_format($this->persentase_kehadiran, 2) . '%',

            // Student relationship
            'siswa' => $this->when(
                $this->relationLoaded('siswa'),
                fn() => new SiswaResource($this->siswa)
            ),

            // Statistics breakdown
            'statistics' => [
                'hadir' => [
                    'total' => $this->total_hadir,
                    'percentage' => $this->total_kehadiran > 0
                        ? round(($this->total_hadir / $this->total_kehadiran) * 100, 2)
                        : 0,
                ],
                'izin' => [
                    'total' => $this->total_izin,
                    'percentage' => $this->total_kehadiran > 0
                        ? round(($this->total_izin / $this->total_kehadiran) * 100, 2)
                        : 0,
                ],
                'sakit' => [
                    'total' => $this->total_sakit,
                    'percentage' => $this->total_kehadiran > 0
                        ? round(($this->total_sakit / $this->total_kehadiran) * 100, 2)
                        : 0,
                ],
                'alpha' => [
                    'total' => $this->total_alpha,
                    'percentage' => $this->total_kehadiran > 0
                        ? round(($this->total_alpha / $this->total_kehadiran) * 100, 2)
                        : 0,
                ],
            ],

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Get formatted month name in Indonesian.
     *
     * @return string Formatted month (e.g., "Januari 2024")
     */
    private function getBulanFormatted(): string
    {
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $this->bulan);
            return $date->locale('id')->isoFormat('MMMM YYYY');
        } catch (\Exception $e) {
            return $this->bulan;
        }
    }
}
