<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\SiswaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // Siswa endpoints
    Route::prefix('siswa')->group(function () {
        Route::get('/', [SiswaController::class, 'index']);
        Route::post('/', [SiswaController::class, 'store'])->middleware('role:admin,guru');
        Route::get('/{id}', [SiswaController::class, 'show']);
        Route::put('/{id}', [SiswaController::class, 'update'])->middleware('role:admin,guru');
        Route::delete('/{id}', [SiswaController::class, 'destroy'])->middleware('role:admin');
        Route::get('/{id}/attendance-stats', [SiswaController::class, 'attendanceStats']);
        Route::get('/nis/{nis}', [SiswaController::class, 'findByNis']);
    });

    // Absensi endpoints
    Route::prefix('absensi')->group(function () {
        Route::get('/', [AbsensiController::class, 'index']);
        Route::post('/', [AbsensiController::class, 'store'])->middleware('role:admin,guru');
        Route::post('/bulk', [AbsensiController::class, 'bulkStore'])->middleware('role:admin,guru');
        Route::get('/today', [AbsensiController::class, 'today']);
        Route::get('/statistics', [AbsensiController::class, 'statistics']);
        Route::get('/class-summary', [AbsensiController::class, 'classSummary']);
        Route::get('/{id}', [AbsensiController::class, 'show']);
        Route::put('/{id}', [AbsensiController::class, 'update'])->middleware('role:admin,guru');
        Route::delete('/{id}', [AbsensiController::class, 'destroy'])->middleware('role:admin,guru');
    });

    // Kelas endpoints
    Route::prefix('kelas')->group(function () {
        Route::get('/', [KelasController::class, 'index']);
        Route::post('/', [KelasController::class, 'store'])->middleware('role:admin');
        Route::get('/{id}', [KelasController::class, 'show']);
        Route::put('/{id}', [KelasController::class, 'update'])->middleware('role:admin');
        Route::delete('/{id}', [KelasController::class, 'destroy'])->middleware('role:admin');
        Route::post('/{id}/assign-homeroom', [KelasController::class, 'assignHomeroomTeacher'])->middleware('role:admin');
    });
});
