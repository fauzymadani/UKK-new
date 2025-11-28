<?php

namespace App\Services;

use App\Models\Absensi;
use App\Repositories\Contracts\AbsensiRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbsensiService
 *
 * Service class for handling attendance-related business logic.
 * Manages attendance recording, validation, and reporting.
 *
 * @package App\Services
 */
class AbsensiService
{
    /**
     * @var AbsensiRepositoryInterface
     */
    protected AbsensiRepositoryInterface $absensiRepository;

    /**
     * AbsensiService constructor.
     *
     * @param AbsensiRepositoryInterface $absensiRepository Attendance repository instance
     */
    public function __construct(AbsensiRepositoryInterface $absensiRepository)
    {
        $this->absensiRepository = $absensiRepository;
    }

    /**
     * Get attendance records by student ID.
     *
     * @param int $siswaId Student ID
     * @return Collection Collection of attendance records
     */
    public function getStudentAttendance(int $siswaId): Collection
    {
        return $this->absensiRepository->getBySiswa($siswaId);
    }

    /**
     * Get attendance records for a specific date.
     *
     * @param string $tanggal Date in Y-m-d format
     * @return Collection Collection of attendance records for the date
     */
    public function getAttendanceByDate(string $tanggal): Collection
    {
        return $this->absensiRepository->getByTanggal($tanggal);
    }

    /**
     * Get attendance records for a specific month.
     *
     * @param int $bulan Month (1-12)
     * @param int $tahun Year (e.g., 2024)
     * @return Collection Collection of attendance records for the month
     */
    public function getAttendanceByMonth(int $bulan, int $tahun): Collection
    {
        return $this->absensiRepository->getByBulan($bulan, $tahun);
    }

    /**
     * Record attendance for a student.
     *
     * Validates the attendance data and records it in the database.
     * If attendance already exists, it will be updated.
     *
     * @param array $data Attendance data
     * @return Model Created or updated attendance record
     * @throws \InvalidArgumentException If validation fails
     */
    public function recordAttendance(array $data): Model
    {
        // Validate status
        $validStatuses = ['hadir', 'izin', 'sakit', 'alpha'];
        if (!in_array($data['status'], $validStatuses)) {
            throw new \InvalidArgumentException("Invalid attendance status: {$data['status']}");
        }

        // Set current time if not provided
        if (!isset($data['waktu_absen']) && $data['status'] === 'hadir') {
            $data['waktu_absen'] = Carbon::now()->format('H:i:s');
        }

        // Set default date to today if not provided
        if (!isset($data['tanggal'])) {
            $data['tanggal'] = Carbon::now()->format('Y-m-d');
        }

        return $this->absensiRepository->recordAttendance($data);
    }

    /**
     * Record bulk attendance for multiple students.
     *
     * @param array $attendanceData Array of attendance data for multiple students
     * @return array Array of results with success and error records
     */
    public function recordBulkAttendance(array $attendanceData): array
    {
        $results = [
            'success' => [],
            'errors' => [],
        ];

        foreach ($attendanceData as $data) {
            try {
                $attendance = $this->recordAttendance($data);
                $results['success'][] = $attendance;
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'data' => $data,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Check if attendance already exists.
     *
     * @param int $siswaId Student ID
     * @param int $jadwalId Schedule ID
     * @param string $tanggal Date in Y-m-d format
     * @return bool True if attendance exists, false otherwise
     */
    public function attendanceExists(int $siswaId, int $jadwalId, string $tanggal): bool
    {
        return $this->absensiRepository->exists($siswaId, $jadwalId, $tanggal);
    }

    /**
     * Get attendance statistics for a student.
     *
     * Returns detailed statistics including total present, sick, permission, and absent.
     *
     * @param int $siswaId Student ID
     * @param string|null $startDate Start date (Y-m-d format)
     * @param string|null $endDate End date (Y-m-d format)
     * @return array Array containing attendance statistics
     */
    public function getStudentStatistics(int $siswaId, ?string $startDate = null, ?string $endDate = null): array
    {
        return $this->absensiRepository->getStatistics($siswaId, $startDate, $endDate);
    }

    /**
     * Get today's attendance records.
     *
     * @return Collection Collection of today's attendance records
     */
    public function getTodayAttendance(): Collection
    {
        return $this->getAttendanceByDate(Carbon::now()->format('Y-m-d'));
    }

    /**
     * Get attendance summary by class for a specific date.
     *
     * @param int $kelasId Class ID
     * @param string|null $tanggal Date (Y-m-d format), defaults to today
     * @return array Attendance summary with statistics
     */
    public function getClassAttendanceSummary(int $kelasId, ?string $tanggal = null): array
    {
        $tanggal = $tanggal ?? Carbon::now()->format('Y-m-d');

        $attendance = $this->absensiRepository->getByTanggal($tanggal);

        // Filter by class
        $classAttendance = $attendance->filter(function ($record) use ($kelasId) {
            return $record->siswa->kelas_id === $kelasId;
        });

        return [
            'tanggal' => $tanggal,
            'kelas_id' => $kelasId,
            'total_siswa' => $classAttendance->count(),
            'hadir' => $classAttendance->where('status', 'hadir')->count(),
            'izin' => $classAttendance->where('status', 'izin')->count(),
            'sakit' => $classAttendance->where('status', 'sakit')->count(),
            'alpha' => $classAttendance->where('status', 'alpha')->count(),
            'persentase_kehadiran' => $classAttendance->count() > 0
                ? round(($classAttendance->where('status', 'hadir')->count() / $classAttendance->count()) * 100, 2)
                : 0,
        ];
    }

    /**
     * Update attendance status.
     *
     * @param int $id Attendance record ID
     * @param array $data Updated data
     * @return bool True if update successful, false otherwise
     */
    public function updateAttendance(int $id, array $data): bool
    {
        return $this->absensiRepository->update($id, $data);
    }

    /**
     * Delete an attendance record.
     *
     * @param int $id Attendance record ID
     * @return bool True if deletion successful, false otherwise
     */
    public function deleteAttendance(int $id): bool
    {
        return $this->absensiRepository->delete($id);
    }
}
