<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JadwalGuru;
use App\Models\JadwalPiketGuru;
use App\Models\Siswa;

class JadwalController extends Controller
{
    // ================== INDEX ==================
    public function index()
    {
        // Ambil semua jadwal tanpa login
        $jadwalMengajar = JadwalGuru::all();
        $jadwalPiket   = JadwalPiketGuru::all();

        return view('guru.jadwal.index', compact('jadwalMengajar', 'jadwalPiket'));
    }

    // ================== CREATE ==================
    public function create()
    {
        $kelas = Siswa::select('kelas')->distinct()->pluck('kelas');
        return view('guru.jadwal.create', compact('kelas'));
    }

    // ================== STORE JADWAL PIKET ==================
    public function storePiket(Request $request)
    {
        $request->validate([
            'id_guru'    => 'required|integer',
            'hari'       => 'required',
            'jam_mulai'  => 'required',
            'jam_selesai'=> 'required',
        ]);

        JadwalPiketGuru::create([
            'id_guru'     => $request->id_guru,
            'hari'        => $request->hari,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan'  => $request->keterangan,
        ]);

        return redirect()->route('guru.jadwal.index')->with('success', '✅ Jadwal Piket berhasil ditambahkan');
    }

    // ================== STORE JADWAL MENGAJAR ==================
    public function storeMengajar(Request $request)
    {
        $request->validate([
            'id_guru'       => 'required|integer',
            'hari'          => 'required',
            'jam_mulai'     => 'required',
            'jam_selesai'   => 'required',
            'mata_pelajaran'=> 'required',
            'kelas'         => 'required',
        ]);

        JadwalGuru::create([
            'id_guru'       => $request->id_guru,
            'hari'          => $request->hari,
            'jam_mulai'     => $request->jam_mulai,
            'jam_selesai'   => $request->jam_selesai,
            'mata_pelajaran'=> $request->mata_pelajaran,
            'kelas'         => $request->kelas,
        ]);

        return redirect()->route('guru.jadwal.index')->with('success', '✅ Jadwal Mengajar berhasil ditambahkan');
    }

    // ================== EDIT PIKET ==================
    public function editPiket($id, Request $request)
    {
        $jadwal = JadwalPiketGuru::where('id_guru', $request->session()->get('id_guru'))
            ->findOrFail($id);

        return view('guru.jadwal-piket.edit', compact('jadwal'));
    }

    // ================== UPDATE PIKET ==================
    public function updatePiket(Request $request, $id)
    {
        $jadwal = JadwalPiketGuru::where('id_guru', $request->session()->get('id_guru'))
            ->findOrFail($id);

        $request->validate([
            'hari'        => 'required',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'keterangan'  => 'nullable|string',
        ]);

        $jadwal->update($request->all());

        return redirect()->route('guru.jadwal.index')
            ->with('success', 'Jadwal piket berhasil diperbarui!');
    }

    // ================== HAPUS PIKET ==================
    public function destroyPiket($id, Request $request)
    {
        $jadwal = JadwalPiketGuru::where('id_guru', $request->session()->get('id_guru'))
            ->findOrFail($id);

        $jadwal->delete();

        return redirect()->route('guru.jadwal.index')
            ->with('success', 'Jadwal piket berhasil dihapus!');
    }

    // ================== EDIT MENGAJAR ==================
    public function editMengajar($id, Request $request)
    {
        $jadwal = JadwalGuru::where('id_guru', $request->session()->get('id_guru'))
            ->findOrFail($id);

        return view('guru.jadwal-guru.edit', compact('jadwal'));
    }

    // ================== UPDATE MENGAJAR ==================
    public function updateMengajar(Request $request, $id)
    {
        $jadwal = JadwalGuru::where('id_guru', $request->session()->get('id_guru'))
            ->findOrFail($id);

        $request->validate([
            'hari'            => 'required',
            'jam_mulai'       => 'required',
            'jam_selesai'     => 'required',
            'mata_pelajaran'  => 'required|string',
            'kelas'           => 'required|string',
        ]);

        $jadwal->update($request->all());

        return redirect()->route('guru.jadwal.index')
            ->with('success', 'Jadwal mengajar berhasil diperbarui!');
    }

    // ================== HAPUS MENGAJAR ==================
    public function destroyMengajar($id, Request $request)
    {
        $jadwal = JadwalGuru::where('id_guru', $request->session()->get('id_guru'))
            ->findOrFail($id);

        $jadwal->delete();

        return redirect()->route('guru.jadwal.index')
            ->with('success', 'Jadwal mengajar berhasil dihapus!');
    }
}
