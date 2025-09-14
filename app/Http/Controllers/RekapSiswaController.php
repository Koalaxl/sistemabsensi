<?php

namespace App\Http\Controllers;

use App\Models\KehadiranSiswa;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapSiswaController extends Controller
{
    public function __construct()
    {
        // hanya bisa diakses kalau login
        if (!session()->has('user')) {
            abort(403, 'Silakan login terlebih dahulu.');
        }

        // role yang diizinkan
        $allowedRoles = ['admin', 'guru', 'guru_piket'];
        if (!in_array(session('user.role'), $allowedRoles)) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Halaman rekap siswa (default)
     */
    public function index(Request $request)
{
    $tanggalMulai   = $request->get('tanggal_mulai');
    $tanggalSelesai = $request->get('tanggal_selesai');
    $kelas          = $request->get('kelas');
    $status         = $request->get('status');

    $dataRekap  = collect();
    $statistik  = null;
    $listKelas  = Siswa::select('kelas')
                    ->whereNotNull('kelas')
                    ->distinct()
                    ->orderBy('kelas')
                    ->pluck('kelas')
                    ->toArray();

    if ($tanggalMulai && $tanggalSelesai) {
        if (strtotime($tanggalMulai) > strtotime($tanggalSelesai)) {
            return redirect()->back()->withErrors(['msg' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai']);
        }

        $query = KehadiranSiswa::with(['siswa'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($kelas) {
            $query->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        // paginate
        $dataRekap = $query->orderBy('tanggal', 'desc')
                           ->orderBy('id', 'desc')
                           ->paginate(10);

        // Hitung statistik (tanpa paginate)
        $allData = KehadiranSiswa::with(['siswa'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($kelas) {
            $allData->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        if ($status) {
            $allData->where('status', $status);
        }

        $allData = $allData->get();

        $statistik = [
            'hadir' => $allData->where('status', 'hadir')->count(),
            'izin'  => $allData->where('status', 'izin')->count(),
            'sakit' => $allData->where('status', 'sakit')->count(),
            'alpa'  => $allData->whereIn('status', ['alpa', 'alpha'])->count(),
        ];
    }

    // 🔹 Pilih view sesuai role
    $role = session('user.role');
    if ($role === 'admin') {
        return view('admin.rekap.index', compact(
            'dataRekap', 'statistik', 'listKelas',
            'tanggalMulai', 'tanggalSelesai', 'kelas', 'status'
        ));
    }

    // default guru
    return view('guru.rekap_siswa.index', compact(
        'dataRekap', 'statistik', 'listKelas',
        'tanggalMulai', 'tanggalSelesai', 'kelas', 'status'
    ));
}


    /**
     * Export rekap siswa ke Excel / PDF
     */
    public function export(Request $request)
    {
        $tanggalMulai   = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');
        $kelas          = $request->get('kelas');
        $status         = $request->get('status');
        $exportType     = $request->get('export', 'excel'); 

        if (!$tanggalMulai || !$tanggalSelesai) {
            return redirect()->back()->withErrors(['msg' => 'Parameter tidak lengkap untuk export']);
        }

        $query = KehadiranSiswa::with(['siswa'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($kelas) {
            $query->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $dataRekap = $query->orderBy('tanggal', 'asc')->get();

        if ($exportType == 'excel') {
            return $this->exportToExcel($dataRekap, $tanggalMulai, $tanggalSelesai, $kelas, $status);
        } else {
            return $this->exportToPDF($dataRekap, $tanggalMulai, $tanggalSelesai, $kelas, $status);
        }
    }

    /**
     * Export siswa ke Excel
     */
    private function exportToExcel($dataRekap, $tanggalMulai, $tanggalSelesai, $kelas, $status)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $sheet->setCellValue('A1', 'LAPORAN REKAP KEHADIRAN SISWA');
        $sheet->setCellValue('A2', 'Periode: ' . \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y'));
        if ($kelas) {
            $sheet->setCellValue('A3', 'Kelas: ' . $kelas);
        }
        if ($status) {
            $sheet->setCellValue('A4', 'Status: ' . $status);
        }

        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $startRow = 6;
        $headers = ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Tanggal', 'Status', 'Keterangan'];
        $sheet->fromArray($headers, null, 'A' . $startRow);

        $row = $startRow + 1;
        foreach ($dataRekap as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->siswa->nama_siswa ?? '-');
            $sheet->setCellValue('C' . $row, $item->siswa->nisn ?? '-');
            $sheet->setCellValue('D' . $row, $item->siswa->kelas ?? '-');
            $sheet->setCellValue('E' . $row, \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'));
            $sheet->setCellValue('F' . $row, $item->status);
            $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');
            $row++;
        }

        // Style tabel
        $range = 'A' . $startRow . ':G' . ($row - 1);
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Rekap_Siswa_' . date('Y-m-d_H-i-s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename);
    }

    /**
     * Export siswa ke PDF
     */
    private function exportToPDF($dataRekap, $tanggalMulai, $tanggalSelesai, $kelas, $status)
    {
        $data = [
            'dataRekap'      => $dataRekap,
            'tanggalMulai'   => \Carbon\Carbon::parse($tanggalMulai)->format('d F Y'),
            'tanggalSelesai' => \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y'),
            'kelas'          => $kelas,
            'status'         => $status,
            'tanggal_cetak'  => now()->format('d F Y H:i:s'),
        ];

        $pdf = Pdf::loadView('guru.rekap_siswa.pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Rekap_Siswa_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
