<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\KelasResource;
use App\Services\KelasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class KelasController
 *
 * API controller for managing class operations.
 * Handles CRUD operations for classes and homeroom teacher assignments.
 *
 * @package App\Http\Controllers\Api
 */
class KelasController extends Controller
{
    /**
     * @var KelasService
     */
    protected KelasService $kelasService;

    /**
     * KelasController constructor.
     *
     * @param KelasService $kelasService Class service instance
     */
    public function __construct(KelasService $kelasService)
    {
        $this->kelasService = $kelasService;
    }

    /**
     * Display a listing of classes.
     *
     * @param Request $request The incoming HTTP request
     * @return JsonResponse JSON response with class list
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tingkat = $request->get('tingkat');
            $jurusan = $request->get('jurusan');

            if ($tingkat) {
                $kelas = $this->kelasService->getClassesByGrade($tingkat);
            } elseif ($jurusan) {
                $kelas = $this->kelasService->getClassesByMajor($jurusan);
            } else {
                $kelas = $this->kelasService->getAllClasses();
            }

            return ApiResponse::success(
                KelasResource::collection($kelas),
                'Data kelas berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil data kelas: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Store a newly created class.
     *
     * @param StoreKelasRequest $request Validated request
     * @return JsonResponse JSON response with created class
     */
    public function store(StoreKelasRequest $request): JsonResponse
    {
        try {
            $kelas = $this->kelasService->createClass($request->validated());

            return ApiResponse::success(
                new KelasResource($kelas),
                'Kelas berhasil ditambahkan',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal menambahkan kelas: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified class.
     *
     * @param int $id Class ID
     * @return JsonResponse JSON response with class details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $kelas = $this->kelasService->findClass($id);

            if (!$kelas) {
                return ApiResponse::notFound('Kelas tidak ditemukan');
            }

            return ApiResponse::success(
                new KelasResource($kelas),
                'Data kelas berhasil diambil'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengambil data kelas: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Update the specified class.
     *
     * @param UpdateKelasRequest $request Validated request
     * @param int $id Class ID
     * @return JsonResponse JSON response with update status
     */
    public function update(UpdateKelasRequest $request, int $id): JsonResponse
    {
        try {
            $updated = $this->kelasService->updateClass($id, $request->validated());

            if (!$updated) {
                return ApiResponse::error('Gagal mengupdate kelas', 500);
            }

            $kelas = $this->kelasService->findClass($id);

            return ApiResponse::success(
                new KelasResource($kelas),
                'Data kelas berhasil diupdate'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal mengupdate kelas: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified class.
     *
     * @param int $id Class ID
     * @return JsonResponse JSON response with deletion status
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->kelasService->deleteClass($id);

            if (!$deleted) {
                return ApiResponse::error('Gagal menghapus kelas', 500);
            }

            return ApiResponse::success(
                null,
                'Kelas berhasil dihapus'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal menghapus kelas: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Assign homeroom teacher to a class.
     *
     * @param Request $request The incoming HTTP request
     * @param int $id Class ID
     * @return JsonResponse JSON response with assignment status
     */
    public function assignHomeroomTeacher(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'wali_kelas_id' => 'required|integer|exists:users,id',
            ]);

            $assigned = $this->kelasService->assignHomeroomTeacher(
                $id,
                $request->wali_kelas_id
            );

            if (!$assigned) {
                return ApiResponse::error('Gagal menugaskan wali kelas', 500);
            }

            return ApiResponse::success(
                null,
                'Wali kelas berhasil ditugaskan'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Gagal menugaskan wali kelas: ' . $e->getMessage(),
                500
            );
        }
    }
}
