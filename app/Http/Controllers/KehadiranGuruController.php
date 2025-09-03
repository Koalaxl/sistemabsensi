<?php

namespace App\Http\Controllers;

use App\Models\KehadiranGuru;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class KehadiranGuruController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->session()->get('login_role');
        $loginId = $request->session()->get('login_id');

        // ✅ ADMIN
        if ($role === 'admin') {
            $query = KehadiranGuru::with('guru');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('guru', function ($q) use ($search) {
                    $q->where('nama_guru', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('mata_pelajaran', 'like', "%{$search}%");
                });
            }

            $kehadiran = $query->orderBy('tanggal', 'desc')->get();
            $listStatus = ['Hadir','Izin','Sakit','Alpha'];
            return view('admin.kehadiran-guru.index', compact('kehadiran','listStatus'));
        }

        // ✅ GURU
        if ($role === 'guru') {
            $guruId = $loginId;
            $query = KehadiranGuru::where('id_guru', $guruId);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            $kehadiran = $query->orderBy('tanggal','desc')->get();
            $listStatus = ['Hadir','Izin','Sakit','Alpha'];
            return view('guru.absensi.index', compact('kehadiran','listStatus'));
        }

        abort(403);
    }

    public function create(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            abort(403);
        }

        $guru = Guru::all();
        $listStatus = ['Hadir', 'Izin', 'Sakit', 'Alpha'];
        return view('admin.kehadiran-guru.create', compact('guru', 'listStatus'));
    }

    public function store(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            abort(403);
        }

        $request->validate([
            'id_guru'    => 'required|exists:guru,id_guru',
            'tanggal'    => 'required|date',
            'status'     => 'required|string|in:Hadir,Izin,Sakit,Alpha',
            'keterangan' => 'nullable|string',
        ]);

        $cek = KehadiranGuru::where('id_guru', $request->id_guru)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($cek) {
            return redirect()->back()
                ->withInput()
                ->with('error', '⚠️ Guru ini sudah memiliki data kehadiran pada tanggal tersebut.');
        }

        KehadiranGuru::create($request->only(['id_guru', 'tanggal', 'status', 'keterangan']));

        return redirect()->route('admin.kehadiran-guru.index')
            ->with('success', '✅ Data kehadiran guru berhasil ditambahkan.');
    }

    public function edit(Request $request, KehadiranGuru $kehadiran_guru)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            abort(403);
        }

        $guru = Guru::all();
        $listStatus = ['Hadir', 'Izin', 'Sakit', 'Alpha'];
        return view('admin.kehadiran-guru.edit', compact('kehadiran_guru', 'guru', 'listStatus'));
    }

    public function update(Request $request, KehadiranGuru $kehadiran_guru)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            abort(403);
        }

        $request->validate([
            'id_guru'    => 'required|exists:guru,id_guru',
            'tanggal'    => 'required|date',
            'status'     => 'required|string|in:Hadir,Izin,Sakit,Alpha',
            'keterangan' => 'nullable|string',
        ]);

        $kehadiran_guru->update($request->only(['id_guru', 'tanggal', 'status', 'keterangan']));

        return redirect()->route('admin.kehadiran-guru.index')
            ->with('success', 'Data kehadiran guru berhasil diperbarui.');
    }

    public function destroy(Request $request, KehadiranGuru $kehadiran_guru)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            abort(403);
        }

        $kehadiran_guru->delete();
        return redirect()->route('admin.kehadiran-guru.index')
            ->with('success', 'Data kehadiran guru berhasil dihapus.');
    }

    public function hapusSemua(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            abort(403);
        }

        DB::table('kehadiran_guru')->delete();

        return redirect()->route('admin.kehadiran-guru.index')
            ->with('success', 'Semua data kehadiran guru berhasil dihapus.');
    }

    public function rekap(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            abort(403);
        }

        $bulan = $request->bulan ?? Carbon::now()->format('Y-m');
        $dataRekap = KehadiranGuru::with('guru')
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->get();

        $statistik = [
            'hadir' => $dataRekap->where('status', 'Hadir')->count(),
            'izin'  => $dataRekap->where('status', 'Izin')->count(),
            'sakit' => $dataRekap->where('status', 'Sakit')->count(),
            'alpha' => $dataRekap->where('status', 'Alpha')->count(),
        ];

        return view('admin.kehadiran-guru.rekap', compact('dataRekap', 'bulan', 'statistik'));
    }

    public function exportToPDF(Request $request)
    {
        if ($request->session()->get('login_role') !== 'admin') {
            abort(403);
        }

        $bulan = $request->bulan ?? Carbon::now()->format('Y-m');
        $dataRekap = KehadiranGuru::with('guru')
            ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
            ->get();

        $statistik = [
            'hadir' => $dataRekap->where('status', 'Hadir')->count(),
            'izin'  => $dataRekap->where('status', 'Izin')->count(),
            'sakit' => $dataRekap->where('status', 'Sakit')->count(),
            'alpha' => $dataRekap->where('status', 'Alpha')->count(),
        ];

        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);

        $html = view('admin.kehadiran-guru.pdf', compact('dataRekap', 'bulan', 'statistik'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream("rekap-kehadiran-guru-$bulan.pdf");
    }

    public function absenGuru(Request $request, $status)
    {
        if ($request->session()->get('login_role') !== 'guru') {
            return redirect('/login')->with('error', 'Login dulu sebelum absen.');
        }

        $guruId = $request->session()->get('login_id');
        $tanggal = Carbon::today()->toDateString();

        $cek = KehadiranGuru::where('id_guru', $guruId)
            ->whereDate('tanggal', $tanggal)
            ->first();

        if ($cek) {
            return redirect()->back()->with('error', '⚠️ Anda sudah absen hari ini.');
        }

        KehadiranGuru::create([
            'id_guru' => $guruId,
            'tanggal' => $tanggal,
            'status' => ucfirst($status),
            'keterangan' => null,
        ]);

        return redirect()->back()->with('success', '✅ Absensi berhasil disimpan.');
    }
}
