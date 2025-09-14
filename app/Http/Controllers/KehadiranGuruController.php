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

        // pastikan $bulan selalu tersedia (format: "YYYY-MM")
        $bulan = $request->bulan ?? Carbon::now()->format('Y-m');

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

            // kirim $bulan juga supaya view admin tidak error jika membutuhkan filter bulan
            return view('admin.kehadiran-guru.index', compact('kehadiran','listStatus','bulan'));
        }

        // ✅ GURU
        if ($role === 'guru') {
            $guruId = $loginId;

            // build query untuk guru sekaligus filter bulan
            $query = KehadiranGuru::where('id_guru', $guruId)
                ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan]);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('tanggal')) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            // ambil hasil sekali, terurut
            $kehadiran = $query->orderBy('tanggal', 'desc')->get();

            // Variabel tambahan untuk view (tidak mengubah apapun di view)
            $startOfMonth = Carbon::parse($bulan)->startOfMonth();
            $daysInMonth  = $startOfMonth->daysInMonth;
            $today        = Carbon::today();

            // bangun map tanggal => status dari query kehadiran
            $absensiMap = [];
            foreach ($kehadiran as $k) {
                $absensiMap[Carbon::parse($k->tanggal)->format('Y-m-d')] = $k->status;
            }

            // warna status (sama seperti yang dipakai view sebelumnya)
            $statusColors = [
                'Hadir'  => 'bg-green-500 text-white',
                'Sakit'  => 'bg-blue-500 text-white',
                'Izin'   => 'bg-yellow-400 text-white',
                'Alpha'  => 'bg-red-500 text-white',
                'Kosong' => 'bg-gray-200 text-gray-700',
            ];

            // hitung rekap per hari (dan tandai Kosong => Alpha untuk tanggal lampau)
            $rekap = ['Hadir'=>0,'Sakit'=>0,'Izin'=>0,'Alpha'=>0,'Kosong'=>0];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $date = $startOfMonth->copy()->day($day);
                $dateStr = $date->format('Y-m-d');
                $status = $absensiMap[$dateStr] ?? 'Kosong';

                // Jika kosong tapi tanggal sudah lewat → dianggap Alpha
                if ($status === 'Kosong' && $date->lt($today)) {
                    $status = 'Alpha';
                }

                if (! isset($rekap[$status])) {
                    $rekap[$status] = 0;
                }
                $rekap[$status]++;

                // update supaya view bisa langsung membaca status final per tanggal
                $absensiMap[$dateStr] = $status;
            }

            $listStatus = ['Hadir','Izin','Sakit','Alpha'];

            return view('guru.absensi.index', compact(
                'kehadiran','listStatus','bulan',
                'startOfMonth','daysInMonth','today',
                'absensiMap','statusColors','rekap'
            ));
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
