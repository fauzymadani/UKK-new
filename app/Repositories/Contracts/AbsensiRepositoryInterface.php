<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface AbsensiRepositoryInterface
 *
 * Repository interface for attendance-related database operations.
 *
 * @package App\Repositories\Contracts
 */
interface AbsensiRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get attendance records by student ID.
     *
     * @param int $siswaId Student ID
     * @return Collection
     */
    public function getBySiswa(int $siswaId): Collection;

    /**
     * Get attendance records by date.
     *
     * @param string $tanggal Date in Y-m-d format
     * @return Collection
     */
    public function getByTanggal(string $tanggal): Collection;

    /**
     * Get attendance records by month and year.
     *
     * @param int $bulan Month (1-12)
     * @param int $tahun Year
     * @return Collection
     */
    public function getByBulan(int $bulan, int $tahun): Collection;

    /**
     * Record attendance for a student.
     *
     * @param array $data Attendance data
     * @return Model
     */
    public function recordAttendance(array $data): Model;

    /**
     * Check if attendance already exists for a student on a specific date and schedule.
     *
     * @param int $siswaId Student ID
     * @param int $jadwalId Schedule ID
     * @param string $tanggal Date in Y-m-d format
     * @return bool
     */
    public function exists(int $siswaId, int $jadwalId, string $tanggal): bool;

    /**
     * Get attendance statistics for a student in a specific period.
     *
     * @param int $siswaId Student ID
     * @param string|null $startDate Start date (Y-m-d format)
     * @param string|null $endDate End date (Y-m-d format)
     * @return array
     */
    public function getStatistics(int $siswaId, ?string $startDate = null, ?string $endDate = null): array;
}
