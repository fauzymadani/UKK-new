<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Guru\Dashboard;
use App\Livewire\Guru\InputAbsensi;
use App\Livewire\Guru\JadwalMengajar;
use App\Livewire\Guru\RekapAbsensi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Login routes
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
| Guru Panel Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/input-absensi', InputAbsensi::class)->name('input-absensi');
    Route::get('/rekap-absensi', RekapAbsensi::class)->name('rekap-absensi');
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
