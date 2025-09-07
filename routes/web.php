<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\JadwalPiketGuruController;
use App\Http\Controllers\JadwalGuruController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\KehadiranGuruController;
use App\Http\Controllers\KehadiranSiswaController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AgendaKelasController;
use App\Http\Controllers\JadwalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman awal → login
Route::redirect('/', '/login');

// Login & Logout
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Middleware sederhana cek session & role
function cekLoginRole(Request $request, $role)
{
    if (!$request->session()->has('login_role') || $request->session()->get('login_role') !== $role) {
        return redirect()->route('login')->withErrors([
            'msg' => 'Silakan login sebagai ' . ucfirst(str_replace('_', ' ', $role))
        ]);
    }
    return null;
}

    /* ==================== ADMIN ==================== */
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function (Request $request) {
            if ($redirect = cekLoginRole($request, 'admin')) return $redirect;
            return app(DashboardController::class)->admin($request);
        })->name('dashboard');

        // CRUD Jadwal Guru
        Route::resource('jadwal-guru', JadwalGuruController::class)
            ->parameters(['jadwal-guru' => 'id_jadwal'])
            ->names('jadwal-guru');

        // CRUD Jadwal Piket
        Route::resource('jadwal-piket', JadwalPiketGuruController::class)
            ->parameters(['jadwal-piket' => 'id_piket'])
            ->names('jadwal-piket');


        // CRUD Kehadiran Guru
        Route::resource('kehadiran-guru', KehadiranGuruController::class)->names('kehadiran-guru');
        Route::get('kehadiran-guru/export', [KehadiranGuruController::class, 'exportExcel'])
            ->name('kehadiran-guru.export');

        // CRUD Kehadiran Siswa
        Route::resource('kehadiran-siswa', KehadiranSiswaController::class)->names('kehadiran-siswa');
        Route::get('kehadiran-siswa/export', [KehadiranSiswaController::class, 'exportExcel'])
            ->name('kehadiran-siswa.export');

        // Rekap
        Route::get('/rekap', [RekapController::class, 'rekap'])->name('rekap.index');
        Route::get('/rekap/export', [RekapController::class, 'rekapExport'])->name('rekap.export');

        // CRUD Siswa
        Route::resource('siswa', SiswaController::class);
        Route::resource('siswa', SiswaController::class)->except(['show']);

        // CRUD Guru
        Route::resource('guru', GuruController::class);

        // CRUD Wali Kelas
        Route::resource('wali-kelas', WaliKelasController::class);


        // CRUD Pengguna
        Route::resource('pengguna', PenggunaController::class);
        Route::delete('pengguna/hapus-semua', [PenggunaController::class, 'hapusSemua'])->name('pengguna.hapusSemua');
    });
            Route::delete('jadwal-piket/hapus-semua', [JadwalPiketGuruController::class, 'hapusSemua'])
            ->name('jadwal-piket.hapusSemua');
        Route::delete('kehadiran-guru/hapus-by-tanggal', [KehadiranGuruController::class, 'destroyByTanggal'])
            ->name('kehadiran-guru.destroyByTanggal');
        Route::delete('kehadiran-guru/hapus-semua', [KehadiranGuruController::class, 'hapusSemua'])
            ->name('kehadiran-guru.hapusSemua');
        Route::delete('jadwal-guru/hapus-semua', [JadwalGuruController::class, 'hapusSemua'])
            ->name('jadwal-guru.hapusSemua');
        Route::delete('wali-kelas/hapus-semua', [WaliKelasController::class, 'hapusSemua'])->name('wali-kelas.hapusSemua');
        Route::post('wali-kelas/import', [WaliKelasController::class, 'import'])->name('wali-kelas.import');
        Route::delete('guru/hapus-semua', [GuruController::class, 'hapusSemua'])->name('guru.hapusSemua');
        Route::delete('guru/hapus-mata-pelajaran', [GuruController::class, 'destroyByMataPelajaran'])->name('guru.destroyByMataPelajaran');
        Route::post('guru/import', [GuruController::class, 'import'])->name('guru.import');
        Route::delete('kehadiran-siswa/hapus-semua', [KehadiransiswaController::class, 'hapusSemua'])
            ->name('kehadiran-siswa.hapusSemua');
        Route::delete('kehadiran-siswa/hapus-by-tanggal', [KehadiranSiswaController::class, 'destroyByTanggal'])
            ->name('kehadiran-siswa.destroyByTanggal');

        Route::get('siswa/import', [SiswaController::class, 'importForm'])->name('siswa.import.form');
        Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
        Route::get('siswa/template', [SiswaController::class, 'downloadTemplate'])->name('siswa.template');

        Route::delete('siswa/hapus-semua', [SiswaController::class, 'hapusSemua'])->name('siswa.hapusSemua');
        Route::delete('siswa/hapus-kelas', [SiswaController::class, 'destroyByKelas'])->name('siswa.destroyByKelas');
    
    /* ==================== GURU ==================== */
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', function (Request $request) {
            if ($redirect = cekLoginRole($request, 'guru')) return $redirect;
            return app(DashboardController::class)->guru($request);
        })->name('dashboard');

        // Absensi guru
        Route::resource('absensi', KehadiranGuruController::class);

        // Absensi siswa
        Route::resource('absensi-siswa', KehadiranSiswaController::class)
     ->names('absensi_siswa');

        // Jadwal guru
         Route::get('jadwal', [JadwalController::class, 'index'])->name('jadwal.index');

        // Rekap
        Route::get('rekap/absen', [RekapController::class, 'absen'])->name('rekap.absen');

        // Agenda kelas
        Route::get('agenda/kelas', [AgendaKelasController::class, 'index'])->name('agenda.kelas');

        // Absen guru (status: hadir, izin, sakit, dll)
        Route::get('/absen/{status}', [KehadiranGuruController::class, 'absenGuru'])->name('absen.guru');
    });

    /* ==================== GURU PIKET ==================== */
    // Route::prefix('guru-piket')->name('guru-piket.')->group(function () {
    //     Route::get('/dashboard', function (Request $request) {
    //         if ($redirect = cekLoginRole($request, 'guru_piket')) return $redirect;
    //         return app(DashboardController::class)->guruPiket($request);
    //     })->name('dashboard');
    // });

