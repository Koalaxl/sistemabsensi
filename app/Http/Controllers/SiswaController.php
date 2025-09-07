<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;
use Illuminate\Support\Facades\DB;
use App\Exports\SiswaTemplateExport;
use Illuminate\Support\Facades\Log;

class SiswaController extends Controller
{
    public function __construct()
    {
        // ✅ Wajib login
        if (!session()->has('user')) {
            abort(403, 'Silakan login terlebih dahulu.');
        }
    }

    // =========================
    // List siswa
    // =========================
   public function index(Request $request)
    {
        $user = session('user');
        $query = Siswa::query();

        // ✅ Kalau role guru → hanya bisa lihat siswa di kelasnya
        if ($user['role'] === 'guru' && isset($user['id_guru'])) {
            $guru = \App\Models\Guru::find($user['id_guru']);
            if ($guru && $guru->kelas) {
                $query->where('kelas', $guru->kelas);
            }
        }

        // ✅ Kalau ada filter kelas dari request
        if ($request->kelas) {
            $query->where('kelas', $request->kelas);
        }

        // ✅ Tambahkan search (cari berdasarkan nisn atau nama_siswa)
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nisn', 'like', '%' . $request->search . '%')
                ->orWhere('nama_siswa', 'like', '%' . $request->search . '%');
            });
        }

        $siswa = $query->get();

        // Ambil semua kelas unik
        $listKelas = Siswa::select('kelas')->distinct()->pluck('kelas');

        return view('admin.siswa.index', compact('siswa', 'listKelas', 'user'));
    }

    public function create()
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa tambah siswa.');
        }

        return view('admin.siswa.create'); // arahkan ke form tambah siswa
    }

    public function store(Request $request)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa menambah siswa.');
        }

        $request->validate([
            'nisn' => 'required|unique:siswa',
            'nama_siswa' => 'required',
            'kelas' => 'required',
            'no_ortu' => 'required|numeric'
        ]);

        Siswa::create($request->all());
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa mengedit siswa.');
        }
        return view('admin.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa mengedit siswa.');
        }

        $request->validate([
            'nisn' => 'required|unique:siswa,nisn,' . $siswa->id,
            'nama_siswa' => 'required',
            'kelas' => 'required',
            'no_ortu' => 'required|numeric'
        ]);

        $siswa->update($request->all());
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa menghapus siswa.');
        }

        $siswa->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }
    public function show(Siswa $siswa)
    {
        $user = session('user');
        // misalnya hanya admin yg boleh lihat detail
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa melihat detail siswa.');
        }

        return view('admin.siswa.show', compact('siswa'));
    }

    // =========================
    // Import & Template
    // =========================
    public function import(Request $request)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa import data siswa.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file'));
            return redirect()->route('admin.siswa.index')
                ->with('success', 'Data siswa berhasil diimport.');
        } catch (\Exception $e) {
            // Simpan error ke log biar bisa dicek developer
            Log::error('Import siswa gagal: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Import gagal! Pastikan format file sesuai template. Error: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa download template.');
        }

        try {
            return Excel::download(new SiswaTemplateExport, 'template_siswa.xlsx');
        } catch (\Exception $e) {
            Log::error('Download template siswa gagal: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Template gagal diunduh. Silakan coba lagi.');
        }
    }

    // =========================
    // Hapus semua
    // =========================
    public function destroyByKelas(Request $request)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa hapus siswa per kelas.');
        }

        Siswa::where('kelas', $request->kelas)->delete();
        return redirect()->route('admin.siswa.index')->with('success', 'Semua siswa di kelas ' . $request->kelas . ' berhasil dihapus.');
    }

    public function hapusSemua()
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa hapus semua siswa.');
        }

        DB::table('kehadiran_siswa')->delete();
        DB::table('siswa')->delete();

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Semua data siswa dan kehadirannya berhasil dihapus.');
    }
}
