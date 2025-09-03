<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\KehadiranSiswa;
use App\Models\KehadiranGuru;
use App\Models\WaliKelas;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        // pastikan hanya admin yang bisa akses
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak!']);
        }

        // Ambil tanggal dari filter, default hari ini
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));

        // --- Untuk Siswa ---
        $totalSiswa = Siswa::count();

        $hadirSiswa = KehadiranSiswa::where('tanggal', $tanggal)->where('status', 'Hadir')->count();
        $izinSiswa  = KehadiranSiswa::where('tanggal', $tanggal)->where('status', 'Izin')->count();
        $sakitSiswa = KehadiranSiswa::where('tanggal', $tanggal)->where('status', 'Sakit')->count();

        // Hitung Alpha
        $alphaSiswa = max(0, $totalSiswa - ($hadirSiswa + $izinSiswa + $sakitSiswa));

        // --- Untuk Guru ---
        $totalGuru = Guru::count();

        $hadirGuru = KehadiranGuru::where('tanggal', $tanggal)->where('status', 'Hadir')->count();
        $izinGuru  = KehadiranGuru::where('tanggal', $tanggal)->where('status', 'Izin')->count();
        $sakitGuru = KehadiranGuru::where('tanggal', $tanggal)->where('status', 'Sakit')->count();

        $alphaGuru = max(0, $totalGuru - ($hadirGuru + $izinGuru + $sakitGuru));

        // Total Wali Kelas
        $totalWaliKelas = WaliKelas::with('guru')->count();

        return view('admin.dashboard', compact(
            'tanggal',
            'hadirSiswa', 'izinSiswa', 'sakitSiswa', 'totalSiswa', 'alphaSiswa',
            'hadirGuru', 'izinGuru', 'sakitGuru', 'totalGuru', 'alphaGuru', 'totalWaliKelas'
        ));
    }

    public function guru(Request $request)
    {
        // hanya guru yang bisa akses
        if ($request->session()->get('login_role') !== 'guru') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak!']);
        }

        $namaGuru = $request->session()->get('login_name');
        $idGuru   = $request->session()->get('id_guru'); // sudah diset di LoginController

        return view('guru.dashboard', compact('namaGuru', 'idGuru'));
    }

    public function guruPiket(Request $request)
    {
        // hanya guru piket yang bisa akses
        if ($request->session()->get('login_role') !== 'guru_piket') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak!']);
        }

        $namaPengguna = $request->session()->get('login_name');

        return view('guru_piket.dashboard', compact('namaPengguna'));
    }
}
