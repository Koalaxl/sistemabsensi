<?php

namespace App\Http\Controllers;

use App\Models\WaliKelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaliKelasController extends Controller
{
    public function __construct()
    {
        // ✅ Cek session login
        if (!session()->has('user')) {
            abort(403, 'Silakan login terlebih dahulu.');
        }
    }

    // =========================
    // List data wali kelas
    // =========================
    public function index(Request $request)
    {
        $user = session('user');
        $query = WaliKelas::with('guru');

        // ✅ Filter pencarian
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('guru', function ($g) use ($search) {
                    $g->where('nama_guru', 'like', "%$search%")
                      ->orWhere('nip', 'like', "%$search%");
                })
                ->orWhere('kelas', 'like', "%$search%");
            });
        }

        $waliKelas = $query->get();

        // ✅ Ambil semua kelas unik
        $listKelas = WaliKelas::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('admin.wali-kelas.index', compact('waliKelas', 'listKelas', 'user'));
    }

    public function create()
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa menambah wali kelas.');
        }

        $guru = Guru::all();
        return view('admin.wali-kelas.create', compact('guru'));
    }

    public function store(Request $request)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa menambah wali kelas.');
        }

        $request->validate([
            'guru_id' => 'required|exists:guru,id_guru',
            'kelas'   => 'required|string'
        ]);

        WaliKelas::create($request->all());
        return redirect()->route('wali-kelas.index')->with('success', 'Data wali kelas berhasil ditambahkan.');
    }

    public function edit(WaliKelas $wali_kelas)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa mengedit wali kelas.');
        }

        $guru = Guru::all();
        return view('admin.wali-kelas.edit', compact('wali_kelas', 'guru'));
    }

    public function update(Request $request, WaliKelas $wali_kelas)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa mengupdate wali kelas.');
        }

        $request->validate([
            'guru_id' => 'required|exists:guru,id_guru',
            'kelas'   => 'required|string'
        ]);

        $wali_kelas->update($request->all());
        return redirect()->route('wali-kelas.index')->with('success', 'Data wali kelas berhasil diperbarui.');
    }

    public function destroy(WaliKelas $wali_kelas)
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa menghapus wali kelas.');
        }

        $wali_kelas->delete();
        return redirect()->route('wali-kelas.index')->with('success', 'Data wali kelas berhasil dihapus.');
    }

    public function hapusSemua()
    {
        $user = session('user');
        if ($user['role'] !== 'admin') {
            abort(403, 'Hanya admin yang bisa menghapus semua wali kelas.');
        }

        DB::table('wali_kelas')->delete();

        return redirect()->route('wali-kelas.index')
            ->with('success', 'Semua data wali kelas berhasil dihapus.');
    }
}
