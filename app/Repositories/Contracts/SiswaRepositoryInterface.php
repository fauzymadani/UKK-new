<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface SiswaRepositoryInterface
 *
 * Repository interface for student-related database operations.
 *
 * @package App\Repositories\Contracts
 */
interface SiswaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get students by class ID.
     *
     * @param int $kelasId Class ID
     * @return Collection
     */
    public function getByKelas(int $kelasId): Collection;

    /**
     * Find student by NIS.
     *
     * @param string $nis Student identification number
     * @return Model|null
     */
    public function findByNis(string $nis): ?Model;

    /**
     * Get students with their relationships loaded.
     *
     * @return Collection
     */
    public function getAllWithRelations(): Collection;

    /**
     * Get student attendance percentage for a specific period.
     *
     * @param int $siswaId Student ID
     * @param string|null $startDate Start date (Y-m-d format)
     * @param string|null $endDate End date (Y-m-d format)
     * @return float
     */
    public function getAttendancePercentage(int $siswaId, ?string $startDate = null, ?string $endDate = null): float;
}
