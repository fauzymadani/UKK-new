<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\SiswaResource;
use App\Services\SiswaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SiswaController
 *
 * API controller for managing student operations.
 * Handles CRUD operations and student-related queries.
 *
 * @package App\Http\Controllers\Api
 */
class SiswaController extends Controller
{
    /**
     * @var SiswaService
     */
    protected SiswaService $siswaService;

    /**
     * SiswaController constructor.
     *
     * @param SiswaService $siswaService Student service instance
     */
    public function __construct(SiswaService $siswaService)
    {
        $this->siswaService = $siswaService;
    }

    /**
     * Display a listing of students.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse JSON response with student list
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);

            if ($request->has('kelas_id')) {
                $siswa = $this->siswaService->getStudentsByClass($request->get('kelas_id'));
                return ApiResponse::success(
                    SiswaResource::collection($siswa),
                    'Data siswa berhasil diambil'
                );
            }

            $siswa = $this->siswaService->getPaginatedStudents($perPage);

            return ApiResponse::success(
                SiswaResource::collection($siswa),
                'Data siswa berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil data siswa: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Store a newly created student in storage.
     *
     * @param StoreSiswaRequest $request Validated request
     * @return JsonResponse JSON response with created student
     */
    public function store(StoreSiswaRequest $request): JsonResponse
    {
        try {
            $siswa = $this->siswaService->createStudent($request->validated());

            return ApiResponse::success(
                new SiswaResource($siswa),
                'Siswa berhasil ditambahkan',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal menambahkan siswa: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified student.
     *
     * @param int $id Student ID
     * @return JsonResponse JSON response with student details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $siswa = $this->siswaService->findStudent($id);

            if (!$siswa) {
                return ApiResponse::notFound('Siswa tidak ditemukan');
            }

            return ApiResponse::success(
                new SiswaResource($siswa),
                'Data siswa berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil data siswa: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Update the specified student in storage.
     *
     * @param UpdateSiswaRequest $request Validated request
     * @param int $id Student ID
     * @return JsonResponse JSON response with update status
     */
    public function update(UpdateSiswaRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->siswaService->updateStudent($id, $request->validated());

            if (!$updated) {
                return ApiResponse::error('Gagal mengupdate siswa', 500);
            }

            $siswa = $this->siswaService->findStudent($id);

            return ApiResponse::success(
                new SiswaResource($siswa),
                'Data siswa berhasil diupdate'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengupdate siswa: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified student from storage.
     *
     * @param int $id Student ID
     * @return JsonResponse JSON response with deletion status
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->siswaService->deleteStudent($id);

            if (!$deleted) {
                return ApiResponse::error('Gagal menghapus siswa', 500);
            }

            return ApiResponse::success(
                null,
                'Siswa berhasil dihapus'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal menghapus siswa: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get student attendance statistics.
     *
     * @param Request $request The incoming HTTP request
     * @param int $id Student ID
     * @return JsonResponse JSON response with attendance statistics
     */
    public function attendanceStats(Request $request, int $id): JsonResponse
    {
        try {
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');

            $percentage = $this->siswaService->getAttendancePercentage($id, $startDate, $endDate);

            return ApiResponse::success([
                'siswa_id' => $id,
                'persentase_kehadiran' => $percentage,
                'periode' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
            ], 'Statistik kehadiran berhasil diambil');
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil statistik kehadiran: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Find student by NIS.
     *
     * @param string $nis Student identification number
     * @return JsonResponse JSON response with student data
     */
    public function findByNis(string $nis): JsonResponse
    {
        try {
            $siswa = $this->siswaService->findStudentByNis($nis);

            if (!$siswa) {
                return ApiResponse::notFound('Siswa dengan NIS tersebut tidak ditemukan');
            }

            return ApiResponse::success(
                new SiswaResource($siswa),
                'Siswa berhasil ditemukan'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mencari siswa: ' . $e->getMessage(),
                500
            );
        }
    }
}
