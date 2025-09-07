<?php
namespace App\Http\Controllers;

use App\Models\JadwalPiketGuru;
use App\Models\JadwalGuru;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->session()->get('login_role') !== 'guru') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak, hanya guru yang bisa membuka halaman ini.']);
        }

        $idGuru = $request->session()->get('id_guru'); 

        // ambil jadwal piket & jadwal mengajar sesuai guru login
        $jadwalPiket = JadwalPiketGuru::where('id_guru', $idGuru)->get();
        $jadwalMengajar = JadwalGuru::where('id_guru', $idGuru)->get();

        return view('guru.jadwal.index', compact('jadwalPiket', 'jadwalMengajar'));
    }
}
