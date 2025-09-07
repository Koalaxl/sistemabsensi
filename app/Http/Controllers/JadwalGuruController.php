<?php
namespace App\Http\Controllers;

use App\Models\JadwalGuru;
use App\Models\Guru;
use Illuminate\Http\Request;

class JadwalGuruController extends Controller
{
    // ADMIN: lihat semua jadwal
    public function index(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak, hanya admin yang bisa membuka halaman ini.']);
        }

        $jadwal = JadwalGuru::with('guru')->get(); 
        return view('admin.jadwal-guru.index', compact('jadwal'));
    }

    public function create(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak.']);
        }

        $guru = Guru::all();
        return view('admin.jadwal-guru.create', compact('guru'));
    }

    public function store(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak.']);
        }

        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'mata_pelajaran' => 'required|string',
            'kelas' => 'required|string',
        ]);

        JadwalGuru::create($request->all());

        return redirect()->route('admin.jadwal-guru.index')->with('success', 'Jadwal guru berhasil ditambahkan');
    }

    public function edit(Request $request, $id)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak.']);
        }

        $jadwal = JadwalGuru::findOrFail($id);
        $guru = Guru::all();
        return view('admin.jadwal-guru.edit', compact('jadwal', 'guru'));
    }

    public function update(Request $request, $id)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak.']);
        }

        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'mata_pelajaran' => 'required|string',
            'kelas' => 'required|string',
        ]);

        $jadwal = JadwalGuru::findOrFail($id);
        $jadwal->update($request->all());

        return redirect()->route('admin.jadwal-guru.index')->with('success', 'Jadwal guru berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak.']);
        }

        $jadwal = JadwalGuru::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('admin.jadwal-guru.index')->with('success', 'Jadwal guru berhasil dihapus');
    }

    public function hapusSemua(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak.']);
        }

        JadwalGuru::truncate(); 
        return redirect()->route('admin.jadwal-guru.index')->with('success', 'Semua jadwal guru berhasil dihapus');
    }
}
