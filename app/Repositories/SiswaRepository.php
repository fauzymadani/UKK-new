<?php

namespace App\Repositories;

use App\Models\Siswa;
use App\Repositories\Contracts\SiswaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiswaRepository
 *
 * Repository for managing student data operations.
 *
 * @package App\Repositories
 */
class SiswaRepository extends BaseRepository implements SiswaRepositoryInterface
{
    /**
     * SiswaRepository constructor.
     *
     * @param Siswa $model
     */
    public function __construct(Siswa $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public function getByKelas(int $kelasId): Collection
    {
        return $this->model
            ->byKelas($kelasId)
            ->with(['user', 'kelas'])
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function findByNis(string $nis): ?Model
    {
        return $this->model
            ->byNis($nis)
            ->with(['user', 'kelas'])
            ->first();
    }

    /**
     * @inheritDoc
     */
    public function getAllWithRelations(): Collection
    {
        return $this->model
            ->withRelations()
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getAttendancePercentage(int $siswaId, ?string $startDate = null, ?string $endDate = null): float
    {
        $query = $this->model->find($siswaId)->absensi();

        if ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }

        $totalAbsensi = $query->count();
        if ($totalAbsensi === 0) {
            return 0.0;
        }

        $totalHadir = (clone $query)->where('status', 'hadir')->count();

        return round(($totalHadir / $totalAbsensi) * 100, 2);
    }
}
