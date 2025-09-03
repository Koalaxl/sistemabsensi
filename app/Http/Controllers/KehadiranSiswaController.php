<?php

namespace App\Http\Controllers;

use App\Models\KehadiranSiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KehadiranSiswaController extends Controller
{
    /**
     * Tampilkan daftar kehadiran siswa
     */
    public function index(Request $request)
    {
        $user = Session::get('user'); // ✅ Ambil dari session

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil list kelas
        if ($user->role === 'admin') {
            $listKelas = Siswa::select('kelas')
                ->whereNotNull('kelas')
                ->distinct()
                ->orderBy('kelas')
                ->pluck('kelas')
                ->toArray();
        } elseif ($user->role === 'guru') {
            $listKelas = $user->kelas ? explode(',', $user->kelas) : [];
            if (empty($listKelas)) {
                $listKelas = Siswa::select('kelas')
                    ->whereNotNull('kelas')
                    ->distinct()
                    ->orderBy('kelas')
                    ->pluck('kelas')
                    ->toArray();
            }
        } else {
            $listKelas = [];
        }

        // Kelas yang dipilih
        $kelasDipilih = $request->kelas ?? ($user->role === 'guru' ? ($user->kelas ? explode(',', $user->kelas)[0] : null) : null);

        // Ambil data siswa untuk form absensi massal (khusus guru)
        $siswa = [];
        if ($user->role === 'guru' && $kelasDipilih) {
            $siswa = Siswa::where('kelas', $kelasDipilih)
                ->orderBy('nama_siswa')
                ->get();
        }

        // Ambil data kehadiran
        $query = KehadiranSiswa::with('siswa');
        if ($kelasDipilih) {
            $query->whereHas('siswa', fn($q) => $q->where('kelas', $kelasDipilih));
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->tanggal) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        $kehadiran = $query->orderBy('tanggal', 'desc')->paginate(15);
        $listStatus = ['Hadir', 'Izin', 'Sakit', 'Alpa'];

        // Pilih view berdasarkan role
        if ($user->role === 'admin') {
            return view('admin.kehadiran-siswa.index', compact(
                'listKelas', 'kelasDipilih', 'kehadiran', 'listStatus'
            ));
        } elseif ($user->role === 'guru') {
            return view('guru.absensi_siswa.index', compact(
                'siswa', 'listKelas', 'kelasDipilih', 'kehadiran', 'listStatus'
            ));
        }

        abort(403); // jika bukan admin/guru
    }

    /**
     * Tampilkan form tambah kehadiran siswa
     */
    public function create()
    {
        $user = Session::get('user');
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $siswa = Siswa::orderBy('nama_siswa')->get();
        $listStatus = ['Hadir', 'Izin', 'Sakit', 'Alpa'];
        
        return view('admin.kehadiran-siswa.create', compact('siswa', 'listStatus'));
    }

    /**
     * Simpan data kehadiran siswa baru
     */
    public function store(Request $request)
{
    // Cek apakah ini input massal (guru)
    if ($request->has('kehadiran')) {
        // Validasi massal
        $request->validate([
            'kelas' => 'required|string',
            'tanggal' => 'required|date',
            'kehadiran' => 'required|array',
            'kehadiran.*.siswa_id' => 'required|exists:siswa,id',
            'kehadiran.*.status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'kehadiran.*.keterangan' => 'nullable|string|max:255',
        ], [
            'kelas.required' => 'Kelas harus dipilih',
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'kehadiran.required' => 'Data kehadiran harus diisi',
            'kehadiran.*.siswa_id.required' => 'Siswa harus dipilih',
            'kehadiran.*.status.required' => 'Status kehadiran harus dipilih',
        ]);

        try {
            DB::beginTransaction();
            $berhasil = 0;
            $sudahAda = 0;

            foreach ($request->kehadiran as $data) {
                // Cek duplikasi per siswa per tanggal
                $existing = KehadiranSiswa::where('siswa_id', $data['siswa_id'])
                    ->whereDate('tanggal', $request->tanggal)
                    ->first();

                if (!$existing) {
                    KehadiranSiswa::create([
                        'siswa_id' => $data['siswa_id'],
                        'tanggal' => $request->tanggal,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]);
                    $berhasil++;
                } else {
                    $sudahAda++;
                }
            }

            DB::commit();

            $message = "Berhasil menyimpan {$berhasil} data kehadiran.";
            if ($sudahAda > 0) {
                $message .= " {$sudahAda} data sudah ada sebelumnya.";
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data! ' . $e->getMessage());
        }
    }

    // Input satu per satu (admin)
    $request->validate([
        'siswa_id'   => 'required|exists:siswa,id',
        'tanggal'    => 'required|date',
        'status'     => 'required|in:Hadir,Izin,Sakit,Alpa',
        'keterangan' => 'nullable|string|max:255',
    ], [
        'siswa_id.required' => 'Siswa harus dipilih',
        'siswa_id.exists' => 'Siswa tidak ditemukan',
        'tanggal.required' => 'Tanggal harus diisi',
        'tanggal.date' => 'Format tanggal tidak valid',
        'status.required' => 'Status kehadiran harus dipilih',
        'status.in' => 'Status kehadiran tidak valid',
        'keterangan.max' => 'Keterangan maksimal 255 karakter',
    ]);

    try {
        // Cek duplikasi
        $existing = KehadiranSiswa::where('siswa_id', $request->siswa_id)
            ->whereDate('tanggal', $request->tanggal)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data kehadiran siswa pada tanggal tersebut sudah ada.');
        }

        KehadiranSiswa::create($request->only(['siswa_id', 'tanggal', 'status', 'keterangan']));

        return redirect()->back()->with('success', 'Data kehadiran siswa berhasil ditambahkan.');

    } catch (\Exception $e) {
        return redirect()->back()->withInput()->with('error', 'Data gagal disimpan! ' . $e->getMessage());
    }
}


    /**
     * Tampilkan form edit kehadiran siswa
     */
    public function edit(KehadiranSiswa $kehadiran_siswa)
    {
        $siswa = Siswa::orderBy('nama_siswa')->get();
        $listStatus = ['Hadir', 'Izin', 'Sakit', 'Alpa'];
        
        return view('admin.kehadiran-siswa.edit', compact('kehadiran_siswa', 'siswa', 'listStatus'));
    }

    /**
     * Update data kehadiran siswa
     */
    public function update(Request $request, KehadiranSiswa $kehadiran_siswa)
    {
        $request->validate([
            'siswa_id'   => 'required|exists:siswa,id',
            'tanggal'    => 'required|date',
            'status'     => 'required|in:Hadir,Izin,Sakit,Alpa',
            'keterangan' => 'nullable|string|max:255',
        ], [
            'siswa_id.required' => 'Siswa harus dipilih',
            'siswa_id.exists' => 'Siswa tidak ditemukan',
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'status.required' => 'Status kehadiran harus dipilih',
            'status.in' => 'Status kehadiran tidak valid',
            'keterangan.max' => 'Keterangan maksimal 255 karakter',
        ]);

        try {
            // Cek duplikasi kecuali data yang sedang diedit
            $existing = KehadiranSiswa::where('siswa_id', $request->siswa_id)
                ->whereDate('tanggal', $request->tanggal)
                ->where('id', '!=', $kehadiran_siswa->id)
                ->first();

            if ($existing) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Data kehadiran siswa pada tanggal tersebut sudah ada.');
            }

            $kehadiran_siswa->update($request->only(['siswa_id', 'tanggal', 'status', 'keterangan']));

            return redirect()->route('admin.kehadiran-siswa.index')
                ->with('success', 'Data kehadiran siswa berhasil diperbarui.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data gagal diperbarui! ' . $e->getMessage());
        }
    }

    /**
     * Hapus data kehadiran siswa
     */
    public function destroy(KehadiranSiswa $kehadiran_siswa)
    {
        $user = Session::get('user');
        if (!$user || $user->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        try {
            $kehadiran_siswa->delete();
            return redirect()->route('admin.kehadiran-siswa.index')
                ->with('success', 'Data kehadiran siswa berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Data gagal dihapus! ' . $e->getMessage());
        }
    }

    /**
     * Hapus semua data kehadiran berdasarkan tanggal
     */
    public function destroyByTanggal(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date'
        ], [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
        ]);

        try {
            $deleted = KehadiranSiswa::whereDate('tanggal', $request->tanggal)->delete();

            if ($deleted > 0) {
                return redirect()->route('admin.kehadiran-siswa.index')
                    ->with('success', "Berhasil menghapus {$deleted} data kehadiran pada tanggal " . \Carbon\Carbon::parse($request->tanggal)->format('d F Y'));
            } else {
                return redirect()->route('admin.kehadiran-siswa.index')
                    ->with('info', 'Tidak ada data kehadiran pada tanggal ' . \Carbon\Carbon::parse($request->tanggal)->format('d F Y'));
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data! ' . $e->getMessage());
        }
    }

    /**
     * Hapus semua data kehadiran siswa
     */
    public function hapusSemua()
    {
        try {
            $deleted = DB::table('kehadiran_siswa')->delete();

            return redirect()->route('admin.kehadiran-siswa.index')
                ->with('success', "Berhasil menghapus semua data kehadiran siswa ({$deleted} record).");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus semua data! ' . $e->getMessage());
        }
    }

    /**
     * Export data kehadiran siswa ke Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            $query = KehadiranSiswa::with('siswa');

            // Filter berdasarkan tanggal jika ada
            if ($request->tanggal) {
                $query->whereDate('tanggal', $request->tanggal);
            }

            // Filter berdasarkan kelas jika ada
            if ($request->kelas) {
                $query->whereHas('siswa', function($q) use ($request) {
                    $q->where('kelas', $request->kelas);
                });
            }

            // Filter berdasarkan status jika ada
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $kehadiran = $query->orderBy('siswa_id')->orderBy('tanggal')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header informasi
            $sheet->setCellValue('A1', 'LAPORAN KEHADIRAN SISWA');
            $sheet->setCellValue('A2', 'Tanggal Cetak: ' . now()->format('d F Y H:i:s'));
            
            if ($request->tanggal) {
                $sheet->setCellValue('A3', 'Tanggal: ' . \Carbon\Carbon::parse($request->tanggal)->format('d F Y'));
            }
            if ($request->kelas) {
                $sheet->setCellValue('A4', 'Kelas: ' . $request->kelas);
            }
            if ($request->status) {
                $sheet->setCellValue('A5', 'Status: ' . $request->status);
            }

            // Style header
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Tentukan baris mulai tabel
            $startRow = 7;

            // Header tabel
            $headers = ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Tanggal', 'Status', 'Keterangan', 'Waktu Input'];
            $sheet->fromArray($headers, null, 'A' . $startRow);

            // Data kehadiran
            $row = $startRow + 1;
            foreach ($kehadiran as $index => $item) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $item->siswa->nisn ?? '-');
                $sheet->setCellValue('C' . $row, $item->siswa->nama_siswa ?? '-');
                $sheet->setCellValue('D' . $row, $item->siswa->kelas ?? '-');
                $sheet->setCellValue('E' . $row, \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'));
                $sheet->setCellValue('F' . $row, $item->status);
                $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');
                $sheet->setCellValue('H' . $row, $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-');
                $row++;
            }

            // Style tabel
            $lastRow = $row - 1;
            $range = 'A' . $startRow . ':H' . $lastRow;
            
            $sheet->getStyle($range)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]);

            // Style header tabel
            $headerRange = 'A' . $startRow . ':H' . $startRow;
            $sheet->getStyle($headerRange)->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E2E2'],
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Auto width
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Statistik
            $statsRow = $lastRow + 3;
            $sheet->setCellValue('A' . $statsRow, 'STATISTIK KEHADIRAN');
            $sheet->getStyle('A' . $statsRow)->getFont()->setBold(true);
            
            $statsRow++;
            $hadir = $kehadiran->where('status', 'Hadir')->count();
            $izin = $kehadiran->where('status', 'Izin')->count();
            $sakit = $kehadiran->where('status', 'Sakit')->count();
            $alpa = $kehadiran->whereIn('status', ['Alpa', 'Alpha'])->count();
            
            $sheet->setCellValue('A' . $statsRow, 'Hadir: ' . $hadir);
            $sheet->setCellValue('B' . $statsRow, 'Izin: ' . $izin);
            $sheet->setCellValue('C' . $statsRow, 'Sakit: ' . $sakit);
            $sheet->setCellValue('D' . $statsRow, 'Alpa: ' . $alpa);
            $sheet->setCellValue('E' . $statsRow, 'Total: ' . $kehadiran->count());

            // Style statistik
            $sheet->getStyle('A' . $statsRow . ':E' . $statsRow)->getFont()->setBold(true);

            $writer = new Xlsx($spreadsheet);

            $fileName = 'Kehadiran_Siswa_' . date('Y-m-d_H-i-s') . '.xlsx';

            // Download file
            return response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal export Excel! ' . $e->getMessage());
        }
    }

    /**
     * Input kehadiran massal berdasarkan kelas
     */
    public function inputMassal()
    {
        $listKelas = Siswa::select('kelas')
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->toArray();

        return view('admin.kehadiran-siswa.input-massal', compact('listKelas'));
    }

    /**
     * Proses input kehadiran massal
     */
    public function storeInputMassal(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
            'tanggal' => 'required|date',
            'kehadiran' => 'required|array',
            'kehadiran.*.siswa_id' => 'required|exists:siswa,id',
            'kehadiran.*.status' => 'required|in:Hadir,Izin,Sakit,Alpa',
            'kehadiran.*.keterangan' => 'nullable|string|max:255',
        ], [
            'kelas.required' => 'Kelas harus dipilih',
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'kehadiran.required' => 'Data kehadiran harus diisi',
        ]);

        try {
            DB::beginTransaction();

            $berhasil = 0;
            $sudahAda = 0;

            foreach ($request->kehadiran as $data) {
                // Cek apakah sudah ada data untuk siswa di tanggal tersebut
                $existing = KehadiranSiswa::where('siswa_id', $data['siswa_id'])
                    ->whereDate('tanggal', $request->tanggal)
                    ->first();

                if (!$existing) {
                    KehadiranSiswa::create([
                        'siswa_id' => $data['siswa_id'],
                        'tanggal' => $request->tanggal,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                    ]);
                    $berhasil++;
                } else {
                    $sudahAda++;
                }
            }

            DB::commit();

            $message = "Berhasil menyimpan {$berhasil} data kehadiran.";
            if ($sudahAda > 0) {
                $message .= " {$sudahAda} data sudah ada sebelumnya.";
            }

            return redirect()->route('admin.kehadiran-siswa.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data! ' . $e->getMessage());
        }
    }

    /**
     * API untuk mendapatkan siswa berdasarkan kelas (untuk input massal)
     */
    public function getSiswaByKelas(Request $request)
    {
        $kelas = $request->get('kelas');
        
        if (!$kelas) {
            return response()->json(['error' => 'Kelas tidak ditemukan'], 400);
        }

        $siswa = Siswa::where('kelas', $kelas)
            ->orderBy('nama_siswa')
            ->get(['id', 'nisn', 'nama_siswa', 'kelas']);

        return response()->json($siswa);
    }

    /**
     * Statistik kehadiran siswa
     */
    public function statistik(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $kelas = $request->get('kelas');

        $query = KehadiranSiswa::with('siswa')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($kelas) {
            $query->whereHas('siswa', function($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        $kehadiran = $query->get();

        // Statistik umum
        $statistikUmum = [
            'hadir' => $kehadiran->where('status', 'Hadir')->count(),
            'izin' => $kehadiran->where('status', 'Izin')->count(),
            'sakit' => $kehadiran->where('status', 'Sakit')->count(),
            'alpa' => $kehadiran->whereIn('status', ['Alpa', 'Alpha'])->count(),
        ];

        // Statistik per siswa
        $statistikPerSiswa = $kehadiran->groupBy('siswa_id')->map(function($items, $siswaId) {
            $siswa = $items->first()->siswa;
            return [
                'siswa' => $siswa,
                'hadir' => $items->where('status', 'Hadir')->count(),
                'izin' => $items->where('status', 'Izin')->count(),
                'sakit' => $items->where('status', 'Sakit')->count(),
                'alpa' => $items->whereIn('status', ['Alpa', 'Alpha'])->count(),
                'total' => $items->count(),
            ];
        })->sortBy('siswa.nama_siswa');

        // Statistik per kelas
        $statistikPerKelas = $kehadiran->groupBy('siswa.kelas')->map(function($items, $kelasNama) {
            return [
                'kelas' => $kelasNama,
                'hadir' => $items->where('status', 'Hadir')->count(),
                'izin' => $items->where('status', 'Izin')->count(),
                'sakit' => $items->where('status', 'Sakit')->count(),
                'alpa' => $items->whereIn('status', ['Alpa', 'Alpha'])->count(),
                'total' => $items->count(),
            ];
        })->sortBy('kelas');

        $listKelas = Siswa::select('kelas')
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->toArray();

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('admin.kehadiran-siswa.statistik', compact(
            'statistikUmum',
            'statistikPerSiswa',
            'statistikPerKelas',
            'listKelas',
            'bulan',
            'tahun',
            'kelas',
            'namaBulan'
        ));
    }

    /**
     * Cetak absensi kosong untuk kelas tertentu
     */
    public function cetakAbsensiKosong(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
        ]);

        try {
            $siswa = Siswa::where('kelas', $request->kelas)
                ->orderBy('nama_siswa')
                ->get();

            if ($siswa->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Tidak ada siswa di kelas ' . $request->kelas);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'DAFTAR HADIR SISWA');
            $sheet->setCellValue('A2', 'Kelas: ' . $request->kelas);
            $sheet->setCellValue('A3', 'Tanggal: _______________');

            // Style header
            $sheet->mergeCells('A1:E1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Header tabel
            $headers = ['No', 'NISN', 'Nama Siswa', 'Tanda Tangan', 'Keterangan'];
            $sheet->fromArray($headers, null, 'A5');

            // Data siswa
            $row = 6;
            foreach ($siswa as $index => $s) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $s->nisn);
                $sheet->setCellValue('C' . $row, $s->nama_siswa);
                $sheet->setCellValue('D' . $row, ''); // Kolom tanda tangan kosong
                $sheet->setCellValue('E' . $row, ''); // Kolom keterangan kosong
                
                // Set tinggi baris untuk tanda tangan
                $sheet->getRowDimension($row)->setRowHeight(30);
                
                $row++;
            }

            // Style tabel
            $lastRow = $row - 1;
            $range = 'A5:E' . $lastRow;
            
            $sheet->getStyle($range)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Style header tabel
            $sheet->getStyle('A5:E5')->applyFromArray([
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E2E2'],
                ],
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Set lebar kolom
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(25);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(20);

            $writer = new Xlsx($spreadsheet);
            $fileName = 'Absensi_Kosong_' . str_replace(' ', '_', $request->kelas) . '_' . date('Y-m-d') . '.xlsx';

            return response()->streamDownload(function() use ($writer) {
                $writer->save('php://output');
            }, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat absensi kosong! ' . $e->getMessage());
        }
    }
}