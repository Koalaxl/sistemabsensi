<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GuruImport;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    // Middleware manual untuk cek session role = admin
    private function cekAdmin(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            return redirect('/login')->withErrors(['msg' => 'Akses ditolak!']);
        }
        return null;
    }

    // Menampilkan daftar guru dengan filter mata pelajaran
    public function index(Request $request)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        $query = Guru::query();

        if ($request->mata_pelajaran) {
            $query->where('mata_pelajaran', $request->mata_pelajaran);
        }

        $guru = $query->get();

        // Ambil semua mata pelajaran unik untuk dropdown filter
        $listMataPelajaran = Guru::select('mata_pelajaran')->distinct()->pluck('mata_pelajaran');

        return view('admin.guru.index', compact('guru', 'listMataPelajaran'));
    }

    public function create(Request $request)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        $request->validate([
            'nama_guru' => 'required',
            'nip' => 'required|unique:guru',
            'mata_pelajaran' => 'required',
        ]);

        Guru::create($request->all());
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Request $request, Guru $guru)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        $request->validate([
            'nip' => 'required|unique:guru,nip,' . $guru->id_guru . ',id_guru',
            'nama_guru' => 'required',
            'mata_pelajaran' => 'required',
        ]);

        $guru->update($request->all());
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Request $request, Guru $guru)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil dihapus.');
    }

    // Import data guru
    public function import(Request $request)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new GuruImport, $request->file('file'));

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diimport.');
    }

    // Hapus semua guru berdasarkan mata pelajaran
    public function destroyByMataPelajaran(Request $request)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        Guru::where('mata_pelajaran', $request->mata_pelajaran)->delete();

        return redirect()->route('guru.index')->with('success', 'Semua guru dengan mata pelajaran ' . $request->mata_pelajaran . ' berhasil dihapus.');
    }

    // Hapus semua data guru
    public function hapusSemua(Request $request)
    {
        if ($redirect = $this->cekAdmin($request)) return $redirect;

        DB::table('guru')->delete();

        return redirect()->route('guru.index')
            ->with('success', 'Semua data guru berhasil dihapus.');
    }
}
