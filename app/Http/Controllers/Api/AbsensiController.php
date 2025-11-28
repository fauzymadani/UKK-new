<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkAbsensiRequest;
use App\Http\Requests\StoreAbsensiRequest;
use App\Http\Requests\UpdateAbsensiRequest;
use App\Http\Resources\AbsensiResource;
use App\Http\Resources\ApiResponse;
use App\Services\AbsensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class AbsensiController
 *
 * API controller for managing attendance operations.
 * Handles attendance recording, updates, and reporting.
 *
 * @package App\Http\Controllers\Api
 */
class AbsensiController extends Controller
{
    /**
     * @var AbsensiService
     */
    protected AbsensiService $absensiService;

    /**
     * AbsensiController constructor.
     *
     * @param AbsensiService $absensiService Attendance service instance
     */
    public function __construct(AbsensiService $absensiService)
    {
        $this->absensiService = $absensiService;
    }

    /**
     * Display a listing of attendance records.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse JSON response with attendance list
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $siswaId = $request->get('siswa_id');
            $tanggal = $request->get('tanggal');
            $bulan = $request->get('bulan');
            $tahun = $request->get('tahun', date('Y'));

            if ($siswaId) {
                $absensi = $this->absensiService->getStudentAttendance($siswaId);
            } elseif ($tanggal) {
                $absensi = $this->absensiService->getAttendanceByDate($tanggal);
            } elseif ($bulan) {
                $absensi = $this->absensiService->getAttendanceByMonth($bulan, $tahun);
            } else {
                $absensi = $this->absensiService->getTodayAttendance();
            }

            return ApiResponse::success(
                AbsensiResource::collection($absensi),
                'Data absensi berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil data absensi: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Store a newly created attendance record.
     *
     * @param StoreAbsensiRequest $request Validated request
     * @return JsonResponse JSON response with created attendance
     */
    public function store(StoreAbsensiRequest $request): JsonResponse
    {
        try {
            // Check if attendance already exists
            if ($this->absensiService->attendanceExists(
                $request->siswa_id,
                $request->jadwal_pelajaran_id,
                $request->tanggal
            )) {
                return ApiResponse::error(
                    'Absensi untuk siswa ini pada jadwal dan tanggal tersebut sudah ada',
                    409
                );
            }

            $absensi = $this->absensiService->recordAttendance($request->validated());

            return ApiResponse::success(
                new AbsensiResource($absensi),
                'Absensi berhasil dicatat',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mencatat absensi: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified attendance record.
     *
     * @param int $id Attendance ID
     * @return JsonResponse JSON response with attendance details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $absensi = $this->absensiService->getStudentAttendance($id)->first();

            if (!$absensi) {
                return ApiResponse::notFound('Data absensi tidak ditemukan');
            }

            return ApiResponse::success(
                new AbsensiResource($absensi),
                'Data absensi berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil data absensi: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Update the specified attendance record.
     *
     * @param UpdateAbsensiRequest $request Validated request
     * @param int $id Attendance ID
     * @return JsonResponse JSON response with update status
     */
    public function update(UpdateAbsensiRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->absensiService->updateAttendance($id, $request->validated());

            if (!$updated) {
                return ApiResponse::error('Gagal mengupdate absensi', 500);
            }

            return ApiResponse::success(
                null,
                'Absensi berhasil diupdate'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengupdate absensi: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified attendance record.
     *
     * @param int $id Attendance ID
     * @return JsonResponse JSON response with deletion status
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->absensiService->deleteAttendance($id);

            if (!$deleted) {
                return ApiResponse::error('Gagal menghapus absensi', 500);
            }

            return ApiResponse::success(
                null,
                'Absensi berhasil dihapus'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal menghapus absensi: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Record bulk attendance for multiple students.
     *
     * @param BulkAbsensiRequest $request Validated request
     * @return JsonResponse JSON response with bulk recording results
     */
    public function bulkStore(BulkAbsensiRequest $request): JsonResponse
    {
        try {
            $results = $this->absensiService->recordBulkAttendance($request->attendance);

            $response = [
                'total_records' => count($request->attendance),
                'success_count' => count($results['success']),
                'error_count' => count($results['errors']),
                'success_data' => AbsensiResource::collection($results['success']),
                'errors' => $results['errors'],
            ];

            return ApiResponse::success(
                $response,
                'Proses absensi massal selesai',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mencatat absensi massal: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get attendance statistics for a student.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse JSON response with attendance statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'siswa_id' => 'required|integer|exists:siswa,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
            ]);

            $stats = $this->absensiService->getStudentStatistics(
                $request->siswa_id,
                $request->start_date,
                $request->end_date
            );

            return ApiResponse::success(
                $stats,
                'Statistik absensi berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil statistik absensi: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get today's attendance records.
     *
     * @return JsonResponse JSON response with today's attendance
     */
    public function today(): JsonResponse
    {
        try {
            $absensi = $this->absensiService->getTodayAttendance();

            return ApiResponse::success(
                AbsensiResource::collection($absensi),
                'Data absensi hari ini berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil data absensi hari ini: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get class attendance summary.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse JSON response with class attendance summary
     */
    public function classSummary(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'kelas_id' => 'required|integer|exists:kelas,id',
                'tanggal' => 'nullable|date',
            ]);

            $summary = $this->absensiService->getClassAttendanceSummary(
                $request->kelas_id,
                $request->tanggal
            );

            return ApiResponse::success(
                $summary,
                'Ringkasan absensi kelas berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil ringkasan absensi kelas: ' . $e->getMessage(),
                500
            );
        }
    }
}
