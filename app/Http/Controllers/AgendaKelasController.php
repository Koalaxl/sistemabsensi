<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaKelasController extends Controller
{
    public function index()
    {
        $agenda = Agenda::with('guru')->orderBy('tanggal', 'desc')->get();
        return view('guru.agenda.index', compact('agenda'));
    }

    public function create()
    {
        $guru = Guru::all();
        $kelas = Siswa::select('kelas')->distinct()->pluck('kelas'); // ✅ ambil kelas unik dari siswa
        return view('guru.agenda.create', compact('guru', 'kelas'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'judul' => 'required|string|max:255',
                'tanggal' => 'required|date',
                'mapel' => 'required|string|max:100',
                'kelas' => 'required|string|max:50',
                'id_guru' => 'required|exists:guru,id_guru',
            ]);

            Agenda::create([
                'judul' => $request->judul,
                'tanggal' => $request->tanggal,
                'mapel' => $request->mapel,
                'kelas' => $request->kelas, // ✅ simpan kelas hasil select
                'id_guru' => $request->id_guru,
            ]);

            return redirect()->route('guru.agenda.index')
                ->with('success', 'Agenda berhasil ditambahkan!');
        } catch (\Throwable $e) {
            return redirect()->route('guru.agenda.index')
                ->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }
}
