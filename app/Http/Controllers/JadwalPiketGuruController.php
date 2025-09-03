<?php
namespace App\Http\Controllers;

use App\Models\JadwalPiketGuru;
use App\Models\Guru;
use Illuminate\Http\Request;

class JadwalPiketGuruController extends Controller
{
    public function index()
    {
        $jadwalPiket = JadwalPiketGuru::with('guru')->get();
        return view('admin.jadwal-piket.index', compact('jadwalPiket'));
    }

    public function create()
    {
        $guru = Guru::all();
        return view('admin.jadwal-piket.create', compact('guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        JadwalPiketGuru::create($request->all());
        return redirect()->route('jadwal-piket.index')->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jadwalPiketGuru = JadwalPiketGuru::findOrFail($id);
        $guru = Guru::all();
        return view('admin.jadwal-piket.edit', compact('jadwalPiketGuru', 'guru'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_guru' => 'required|exists:guru,id_guru',
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'keterangan' => 'nullable|string',
        ]);

        $jadwal = JadwalPiketGuru::findOrFail($id);
        $jadwal->update($request->all());

        return redirect()->route('jadwal-piket.index')->with('success', 'Jadwal piket berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jadwal = JadwalPiketGuru::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('jadwal-piket.index')
            ->with('success', 'Jadwal piket berhasil dihapus.');
    }

    public function hapusSemua()
    {
        JadwalPiketGuru::truncate();
        return redirect()->route('jadwal-piket.index')->with('success', 'Semua jadwal piket berhasil dihapus.');
    }
}
