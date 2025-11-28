<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Guru\Dashboard as GuruDashboard;
use App\Livewire\Guru\InputAbsensi;
use App\Livewire\Guru\JadwalMengajar;
use App\Livewire\Guru\RekapAbsensi;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\ManageUsers;
use App\Livewire\Admin\ManageKelas;
use App\Livewire\Admin\ManageMataPelajaran;
use App\Livewire\Admin\ManageJadwal;
use App\Livewire\Admin\LaporanAbsensi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    /**
     * Dashboard route
     * Shows system overview and statistics
     */
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

    /**
     * Manage Users route
     * CRUD for all users (Admin, Guru, Siswa)
     */
    Route::get('/manage-users', ManageUsers::class)->name('manage-users');

    /**
     * Manage Kelas route
     * CRUD for classes and homeroom teacher assignment
     */
    Route::get('/manage-kelas', ManageKelas::class)->name('manage-kelas');

    /**
     * Manage Mata Pelajaran route
     * CRUD for subjects and teacher assignment
     */
    Route::get('/manage-mata-pelajaran', ManageMataPelajaran::class)->name('manage-mata-pelajaran');

    /**
     * Manage Jadwal route
     * CRUD for class schedules
     */
    Route::get('/manage-jadwal', ManageJadwal::class)->name('manage-jadwal');

    /**
     * Laporan Absensi route
     * Advanced attendance reporting with filters
     */
    Route::get('/laporan-absensi', LaporanAbsensi::class)->name('laporan-absensi');
});

/*
|--------------------------------------------------------------------------
| Guru Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {

    /**
     * Dashboard route
     * Shows today's schedule and attendance summary
     */
    Route::get('/dashboard', GuruDashboard::class)->name('dashboard');

    /**
     * Input Absensi route
     * Form for recording student attendance
     */
    Route::get('/input-absensi', InputAbsensi::class)->name('input-absensi');

    /**
     * Rekap Absensi route
     * View attendance records with filtering
     */
    Route::get('/rekap-absensi', RekapAbsensi::class)->name('rekap-absensi');

    /**
     * Jadwal Mengajar route
     * Display weekly teaching schedule
     */
    Route::get('/jadwal-mengajar', JadwalMengajar::class)->name('jadwal-mengajar');
});

/*
|--------------------------------------------------------------------------
| Role-based Home Redirect
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/home', function () {
    $user = auth()->user();

    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'guru' => redirect()->route('guru.dashboard'),
        'siswa' => redirect()->route('siswa.dashboard'),
        default => abort(403),
    };
})->name('home');
