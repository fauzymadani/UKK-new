<?php

namespace App\Repositories;

use App\Models\Absensi;
use App\Repositories\Contracts\AbsensiRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbsensiRepository
 *
 * Repository for managing attendance data operations.
 *
 * @package App\Repositories
 */
class AbsensiRepository extends BaseRepository implements AbsensiRepositoryInterface
{
    /**
     * AbsensiRepository constructor.
     *
     * @param Absensi $model
     */
    public function __construct(Absensi $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getBySiswa(int $siswaId): Collection
    {
        return $this->model
            ->bySiswa($siswaId)
            ->withRelations()
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getByTanggal(string $tanggal): Collection
    {
        return $this->model
            ->byTanggal($tanggal)
            ->withRelations()
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getByBulan(int $bulan, int $tahun): Collection
    {
        return $this->model
            ->byBulan($bulan, $tahun)
            ->withRelations()
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function recordAttendance(array $data): Model
    {
        return $this->model->updateOrCreate(
            [
                'siswa_id' => $data['siswa_id'],
                'jadwal_pelajaran_id' => $data['jadwal_pelajaran_id'],
                'tanggal' => $data['tanggal'],
            ],
            $data
        );
    }

    /**
     * @inheritDoc
     */
    public function exists(int $siswaId, int $jadwalId, string $tanggal): bool
    {
        return $this->model
            ->where('siswa_id', $siswaId)
            ->where('jadwal_pelajaran_id', $jadwalId)
            ->where('tanggal', $tanggal)
            ->exists();
    }

    /**
     * @inheritDoc
     */
    public function getStatistics(int $siswaId, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = $this->model->bySiswa($siswaId);

        if ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }

        $absensi = $query->get();

        $totalHadir = $absensi->where('status', 'hadir')->count();
        $totalIzin = $absensi->where('status', 'izin')->count();
        $totalSakit = $absensi->where('status', 'sakit')->count();
        $totalAlpha = $absensi->where('status', 'alpha')->count();
        $total = $absensi->count();

        return [
            'total_hadir' => $totalHadir,
            'total_izin' => $totalIzin,
            'total_sakit' => $totalSakit,
            'total_alpha' => $totalAlpha,
            'total_keseluruhan' => $total,
            'persentase_kehadiran' => $total > 0 ? round(($totalHadir / $total) * 100, 2) : 0,
        ];
    }
}
