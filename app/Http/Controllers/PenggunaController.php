<?php
namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function __construct()
    {
        // ✅ Batasi akses hanya untuk admin
        if (session('user.role') !== 'admin') {
            abort(403, 'Akses ditolak. Hanya admin yang bisa mengelola pengguna.');
        }
    }

    public function index()
    {
        $pengguna = Pengguna::with('guru')->get();
        return view('admin.pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        $guru = Guru::all();
        return view('admin.pengguna.create', compact('guru'));
    }

    public function store(Request $request)
    {
        $rules = [
            'username' => 'required|string|max:50|unique:pengguna,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,guru,guru_piket',
        ];

        // ✅ id_guru hanya wajib kalau role guru/guru_piket
        if (in_array($request->role, ['guru', 'guru_piket'])) {
            $rules['id_guru'] = 'required|exists:guru,id_guru';
        }

        $request->validate($rules);

        $namaPengguna = $request->username;
        if ($request->id_guru) {
            $guru = Guru::findOrFail($request->id_guru);
            $namaPengguna = $guru->nama_guru;
        }

        Pengguna::create([
            'nama_pengguna' => $namaPengguna,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'id_guru' => $request->id_guru,
        ]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(Pengguna $pengguna)
    {
        $guru = Guru::all();
        return view('admin.pengguna.edit', compact('pengguna', 'guru'));
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $rules = [
            'nama_pengguna' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:pengguna,username,' . $pengguna->id_pengguna . ',id_pengguna',
            'role' => 'required|in:admin,guru,guru_piket',
        ];

        if (in_array($request->role, ['guru', 'guru_piket'])) {
            $rules['id_guru'] = 'required|exists:guru,id_guru';
        }

        $request->validate($rules);

        $data = [
            'nama_pengguna' => $request->nama_pengguna,
            'username' => $request->username,
            'role' => $request->role,
            'id_guru' => $request->id_guru,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete();
        return redirect()->route('admin.pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function hapusSemua()
    {
        Pengguna::truncate();
        return redirect()->route('admin.pengguna.index')->with('success', 'Semua data pengguna berhasil dihapus.');
    }
}
