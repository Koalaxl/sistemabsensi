<?php

namespace App\Http\Controllers;

use App\Models\KehadiranSiswa;
use App\Models\KehadiranGuru;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapController extends Controller
{
    /**
     * Tampilan halaman rekap kehadiran
     */
    public function __construct()
    {
        // ✅ Pastikan hanya user login yang bisa akses
        if (!session()->has('user')) {
            abort(403, 'Silakan login terlebih dahulu.');
        }

        // ✅ Batasi role yang boleh akses rekap
        $allowedRoles = ['admin', 'guru', 'guru_piket'];
        if (!in_array(session('user.role'), $allowedRoles)) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Tampilan halaman rekap kehadiran
     */
    public function rekap()
    {
        $userRole = session('user.role');
        $userIdGuru = session('user.id_guru');

        // Ambil data rekap guru
        $rekapGuru = KehadiranGuru::selectRaw('id_guru, COUNT(*) as total, 
                        SUM(CASE WHEN LOWER(status) = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN LOWER(status) = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN LOWER(status) = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN LOWER(status) = "alpa" THEN 1 ELSE 0 END) as alpa')
                        ->groupBy('id_guru')
                        ->with('guru');

        // ✅ kalau role guru → hanya tampilkan rekap untuk dirinya
        if ($userRole === 'guru' && $userIdGuru) {
            $rekapGuru->where('id_guru', $userIdGuru);
        }

        $rekapGuru = $rekapGuru->get();

        // Ambil data rekap siswa
        $rekapSiswa = KehadiranSiswa::selectRaw('siswa_id, COUNT(*) as total, 
                        SUM(CASE WHEN LOWER(status) = "hadir" THEN 1 ELSE 0 END) as hadir,
                        SUM(CASE WHEN LOWER(status) = "izin" THEN 1 ELSE 0 END) as izin,
                        SUM(CASE WHEN LOWER(status) = "sakit" THEN 1 ELSE 0 END) as sakit,
                        SUM(CASE WHEN LOWER(status) = "alpa" THEN 1 ELSE 0 END) as alpa')
                        ->groupBy('siswa_id')
                        ->with('siswa');

        // ✅ kalau role guru → bisa dibatasi ke kelas tertentu
        if ($userRole === 'guru' && $userIdGuru) {
            $guru = Guru::find($userIdGuru);
            if ($guru && $guru->kelas) {
                $rekapSiswa->whereHas('siswa', function($q) use ($guru) {
                    $q->where('kelas', $guru->kelas);
                });
            }
        }

        $rekapSiswa = $rekapSiswa->get();

        return view('admin.rekap.index', compact('rekapGuru', 'rekapSiswa'));
    }


    public function index(Request $request)
    {
        $jenisRekap = $request->get('jenis_rekap');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');
        $kelas = $request->get('kelas');
        $status = $request->get('status');

        $dataRekap = collect();
        $statistik = null;
        $listKelas = [];

        // Ambil list kelas untuk filter
        $listKelas = Siswa::select('kelas')
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas')
            ->toArray();

        if ($jenisRekap && $tanggalMulai && $tanggalSelesai) {
            // Validasi tanggal
            if (strtotime($tanggalMulai) > strtotime($tanggalSelesai)) {
                return redirect()->back()->withErrors(['msg' => 'Tanggal mulai tidak boleh lebih besar dari tanggal selesai']);
            }

            if ($jenisRekap == 'siswa') {
                $query = KehadiranSiswa::with(['siswa'])
                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

                if ($kelas) {
                    $query->whereHas('siswa', function($q) use ($kelas) {
                        $q->where('kelas', $kelas);
                    });
                }

                if ($status) {
                    $query->where('status', $status);
                }

                $dataRekap = $query->orderBy('tanggal', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();

            } elseif ($jenisRekap == 'guru') {
                $query = KehadiranGuru::with(['guru'])
                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

                if ($status) {
                    $query->where('status', $status);
                }

                $dataRekap = $query->orderBy('tanggal', 'desc')
                    ->orderBy('id_kehadiran_guru', 'desc')
                    ->get();

            } elseif ($jenisRekap == 'gabungan') {
                // Untuk rekap gabungan, kita ambil data siswa dan guru terpisah
                $dataSiswa = KehadiranSiswa::with(['siswa'])
                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
                
                if ($kelas) {
                    $dataSiswa->whereHas('siswa', function($q) use ($kelas) {
                        $q->where('kelas', $kelas);
                    });
                }
                
                if ($status) {
                    $dataSiswa->where('status', $status);
                }
                
                $dataSiswa = $dataSiswa->get();

                $dataGuru = KehadiranGuru::with(['guru'])
                    ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
                
                if ($status) {
                    $dataGuru->where('status', $status);
                }
                
                $dataGuru = $dataGuru->get();

                // Gabungkan data untuk tampilan
                $dataRekap = $dataSiswa->merge($dataGuru)->sortByDesc('tanggal');
            }

            // Hitung statistik dengan case insensitive
            if ($dataRekap->count() > 0) {
                $statistik = [
                    'hadir' => $dataRekap->filter(function($item) {
                        return strtolower($item->status) === 'hadir';
                    })->count(),
                    'izin' => $dataRekap->filter(function($item) {
                        return strtolower($item->status) === 'izin';
                    })->count(),
                    'sakit' => $dataRekap->filter(function($item) {
                        return strtolower($item->status) === 'sakit';
                    })->count(),
                    'alpa' => $dataRekap->filter(function($item) {
                        return strtolower($item->status) === 'alpa' || strtolower($item->status) === 'alpha';
                    })->count(),
                ];
            }
        }

        return view('admin.rekap.index', compact(
            'dataRekap',
            'statistik',
            'listKelas'
        ));
    }

    /**
     * Export rekap ke Excel atau PDF
     */
    public function export(Request $request)
    {
        $jenisRekap = $request->get('jenis_rekap');
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');
        $kelas = $request->get('kelas');
        $status = $request->get('status');
        $exportType = $request->get('export', 'excel'); // excel atau pdf

        if (!$jenisRekap || !$tanggalMulai || !$tanggalSelesai) {
            return redirect()->back()->withErrors(['msg' => 'Parameter tidak lengkap untuk export']);
        }

        // Ambil data sesuai filter
        $dataRekap = collect();
        $dataSiswa = null;
        $dataGuru = null;
        
        if ($jenisRekap == 'siswa') {
            $query = KehadiranSiswa::with(['siswa'])
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

            if ($kelas) {
                $query->whereHas('siswa', function($q) use ($kelas) {
                    $q->where('kelas', $kelas);
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            $dataRekap = $query->orderBy('tanggal', 'asc')->get();

        } elseif ($jenisRekap == 'guru') {
            $query = KehadiranGuru::with(['guru'])
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

            if ($status) {
                $query->where('status', $status);
            }

            $dataRekap = $query->orderBy('tanggal', 'asc')->get();

        } elseif ($jenisRekap == 'gabungan') {
            // Export gabungan akan dibuat dalam sheet terpisah
            $dataSiswa = KehadiranSiswa::with(['siswa'])
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
            
            if ($kelas) {
                $dataSiswa->whereHas('siswa', function($q) use ($kelas) {
                    $q->where('kelas', $kelas);
                });
            }
            
            if ($status) {
                $dataSiswa->where('status', $status);
            }
            
            $dataSiswa = $dataSiswa->orderBy('tanggal', 'asc')->get();

            $dataGuru = KehadiranGuru::with(['guru'])
                ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);
            
            if ($status) {
                $dataGuru->where('status', $status);
            }
            
            $dataGuru = $dataGuru->orderBy('tanggal', 'asc')->get();
        }

        if ($exportType == 'excel') {
            return $this->exportToExcel($jenisRekap, $dataRekap, $dataSiswa, $dataGuru, $tanggalMulai, $tanggalSelesai, $kelas, $status);
        } elseif ($exportType == 'pdf') {
            return $this->exportToPDF($jenisRekap, $dataRekap, $dataSiswa, $dataGuru, $tanggalMulai, $tanggalSelesai, $kelas, $status);
        }

        return redirect()->back()->withErrors(['msg' => 'Format export tidak valid']);
    }

    /**
     * Export ke Excel
     */
    private function exportToExcel($jenisRekap, $dataRekap, $dataSiswa, $dataGuru, $tanggalMulai, $tanggalSelesai, $kelas, $status)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header informasi
        $sheet->setCellValue('A1', 'LAPORAN REKAP KEHADIRAN');
        $sheet->setCellValue('A2', 'Periode: ' . \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y'));
        $sheet->setCellValue('A3', 'Jenis Rekap: ' . ucfirst(str_replace('_', ' ', $jenisRekap)));
        
        $currentRow = 4;
        if ($kelas) {
            $sheet->setCellValue('A' . $currentRow, 'Kelas: ' . $kelas);
            $currentRow++;
        }
        if ($status) {
            $sheet->setCellValue('A' . $currentRow, 'Status: ' . $status);
            $currentRow++;
        }
        
        // Style header
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $startRow = $currentRow + 1;

        if ($jenisRekap == 'gabungan') {
            // Export gabungan dengan sheet terpisah
            return $this->exportGabunganToExcel($spreadsheet, $dataSiswa, $dataGuru, $tanggalMulai, $tanggalSelesai, $kelas, $status);
        }

        // Header tabel
        if ($jenisRekap == 'siswa') {
            $headers = ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Tanggal', 'Status', 'Keterangan'];
            $sheet->fromArray($headers, null, 'A' . $startRow);
            
            // Data siswa
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
        } elseif ($jenisRekap == 'guru') {
            $headers = ['No', 'Nama Guru', 'NIP', 'Mata Pelajaran', 'Tanggal', 'Status', 'Keterangan'];
            $sheet->fromArray($headers, null, 'A' . $startRow);
            
            // Data guru
            $row = $startRow + 1;
            foreach ($dataRekap as $index => $item) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $item->guru->nama_guru ?? '-');
                $sheet->setCellValue('C' . $row, $item->guru->nip ?? '-');
                $sheet->setCellValue('D' . $row, $item->guru->mata_pelajaran ?? '-');
                $sheet->setCellValue('E' . $row, \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'));
                $sheet->setCellValue('F' . $row, $item->status);
                $sheet->setCellValue('G' . $row, $item->keterangan ?? '-');
                $row++;
            }
        }

        // Style tabel
        $this->applyExcelStyles($sheet, $startRow, $row - 1, 'G');

        // Statistik
        $this->addStatistikToExcel($sheet, $dataRekap, $row + 2);

        // Generate filename
        $filename = 'Rekap_Kehadiran_' . ucfirst($jenisRekap) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Output
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Export gabungan ke Excel dengan sheet terpisah
     */
    private function exportGabunganToExcel($spreadsheet, $dataSiswa, $dataGuru, $tanggalMulai, $tanggalSelesai, $kelas, $status)
    {
        // Sheet untuk siswa
        $sheetSiswa = $spreadsheet->getActiveSheet();
        $sheetSiswa->setTitle('Kehadiran Siswa');
        
        // Header informasi siswa
        $sheetSiswa->setCellValue('A1', 'LAPORAN REKAP KEHADIRAN SISWA');
        $sheetSiswa->setCellValue('A2', 'Periode: ' . \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y'));
        
        $currentRow = 3;
        if ($kelas) {
            $sheetSiswa->setCellValue('A' . $currentRow, 'Kelas: ' . $kelas);
            $currentRow++;
        }
        if ($status) {
            $sheetSiswa->setCellValue('A' . $currentRow, 'Status: ' . $status);
            $currentRow++;
        }
        
        $sheetSiswa->mergeCells('A1:G1');
        $sheetSiswa->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheetSiswa->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $startRowSiswa = $currentRow + 1;
        
        // Header tabel siswa
        $headersSiswa = ['No', 'Nama Siswa', 'NISN', 'Kelas', 'Tanggal', 'Status', 'Keterangan'];
        $sheetSiswa->fromArray($headersSiswa, null, 'A' . $startRowSiswa);
        
        // Data siswa
        $row = $startRowSiswa + 1;
        foreach ($dataSiswa as $index => $item) {
            $sheetSiswa->setCellValue('A' . $row, $index + 1);
            $sheetSiswa->setCellValue('B' . $row, $item->siswa->nama_siswa ?? '-');
            $sheetSiswa->setCellValue('C' . $row, $item->siswa->nisn ?? '-');
            $sheetSiswa->setCellValue('D' . $row, $item->siswa->kelas ?? '-');
            $sheetSiswa->setCellValue('E' . $row, \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'));
            $sheetSiswa->setCellValue('F' . $row, $item->status);
            $sheetSiswa->setCellValue('G' . $row, $item->keterangan ?? '-');
            $row++;
        }
        
        // Style sheet siswa
        $this->applyExcelStyles($sheetSiswa, $startRowSiswa, $row - 1, 'G');
        $this->addStatistikToExcel($sheetSiswa, $dataSiswa, $row + 2);
        
        // Sheet untuk guru
        $sheetGuru = $spreadsheet->createSheet();
        $sheetGuru->setTitle('Kehadiran Guru');
        
        // Header informasi guru
        $sheetGuru->setCellValue('A1', 'LAPORAN REKAP KEHADIRAN GURU');
        $sheetGuru->setCellValue('A2', 'Periode: ' . \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y'));
        
        $currentRowGuru = 3;
        if ($status) {
            $sheetGuru->setCellValue('A' . $currentRowGuru, 'Status: ' . $status);
            $currentRowGuru++;
        }
        
        $sheetGuru->mergeCells('A1:G1');
        $sheetGuru->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheetGuru->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $startRowGuru = $currentRowGuru + 1;
        
        // Header tabel guru
        $headersGuru = ['No', 'Nama Guru', 'NIP', 'Mata Pelajaran', 'Tanggal', 'Status', 'Keterangan'];
        $sheetGuru->fromArray($headersGuru, null, 'A' . $startRowGuru);
        
        // Data guru
        $row = $startRowGuru + 1;
        foreach ($dataGuru as $index => $item) {
            $sheetGuru->setCellValue('A' . $row, $index + 1);
            $sheetGuru->setCellValue('B' . $row, $item->guru->nama_guru ?? '-');
            $sheetGuru->setCellValue('C' . $row, $item->guru->nip ?? '-');
            $sheetGuru->setCellValue('D' . $row, $item->guru->mata_pelajaran ?? '-');
            $sheetGuru->setCellValue('E' . $row, \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'));
            $sheetGuru->setCellValue('F' . $row, $item->status);
            $sheetGuru->setCellValue('G' . $row, $item->keterangan ?? '-');
            $row++;
        }
        
        // Style sheet guru
        $this->applyExcelStyles($sheetGuru, $startRowGuru, $row - 1, 'G');
        $this->addStatistikToExcel($sheetGuru, $dataGuru, $row + 2);
        
        // Set active sheet ke siswa
        $spreadsheet->setActiveSheetIndex(0);
        
        // Generate filename
        $filename = 'Rekap_Kehadiran_Gabungan_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        // Output
        $writer = new Xlsx($spreadsheet);
        
        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Apply styles to Excel sheet
     */
    private function applyExcelStyles($sheet, $startRow, $endRow, $lastColumn)
    {
        // Border untuk tabel
        $range = 'A' . $startRow . ':' . $lastColumn . $endRow;
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        // Style header tabel
        $headerRange = 'A' . $startRow . ':' . $lastColumn . $startRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE2E2E2'],
            ],
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Auto width
        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    /**
     * Tambahkan statistik ke Excel
     */
    private function addStatistikToExcel($sheet, $data, $startRow)
    {
        $sheet->setCellValue('A' . $startRow, 'STATISTIK KEHADIRAN');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true);
        
        $statsRow = $startRow + 1;
        $hadir = $data->where('status', 'Hadir')->count();
        $izin = $data->where('status', 'Izin')->count();
        $sakit = $data->where('status', 'Sakit')->count();
        $alpa = $data->whereIn('status', ['Alpa', 'Alpha'])->count();
        
        $sheet->setCellValue('A' . $statsRow, 'Hadir: ' . $hadir);
        $sheet->setCellValue('B' . $statsRow, 'Izin: ' . $izin);
        $sheet->setCellValue('C' . $statsRow, 'Sakit: ' . $sakit);
        $sheet->setCellValue('D' . $statsRow, 'Alpa: ' . $alpa);

        // Style statistik
        $sheet->getStyle('A' . $statsRow . ':D' . $statsRow)->getFont()->setBold(true);
    }

    /**
     * Export ke PDF
     */
    private function exportToPDF($jenisRekap, $dataRekap, $dataSiswa, $dataGuru, $tanggalMulai, $tanggalSelesai, $kelas, $status)
    {
        // Hitung statistik untuk ditampilkan di PDF
        $statistik = [];
        
        if ($jenisRekap == 'gabungan') {
            $allData = collect($dataSiswa)->merge($dataGuru);
            $statistik = [
                'hadir' => $allData->where('status', 'Hadir')->count(),
                'izin' => $allData->where('status', 'Izin')->count(),
                'sakit' => $allData->where('status', 'Sakit')->count(),
                'alpa' => $allData->whereIn('status', ['Alpa', 'Alpha'])->count(),
            ];
        } else {
            $statistik = [
                'hadir' => $dataRekap->where('status', 'Hadir')->count(),
                'izin' => $dataRekap->where('status', 'Izin')->count(),
                'sakit' => $dataRekap->where('status', 'Sakit')->count(),
                'alpa' => $dataRekap->whereIn('status', ['Alpa', 'Alpha'])->count(),
            ];
        }

        $data = [
            'jenisRekap' => $jenisRekap,
            'dataRekap' => $dataRekap,
            'dataSiswa' => $dataSiswa,
            'dataGuru' => $dataGuru,
            'tanggalMulai' => \Carbon\Carbon::parse($tanggalMulai)->format('d F Y'),
            'tanggalSelesai' => \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y'),
            'kelas' => $kelas,
            'status' => $status,
            'statistik' => $statistik,
            'tanggal_cetak' => now()->format('d F Y H:i:s'),
        ];

        try {
            // Menggunakan Facade PDF
            $pdf = Pdf::loadView('admin.rekap.pdf', $data);
            
            // Set orientasi dan ukuran kertas
            $pdf->setPaper('A4', 'landscape');
            
            // Set options untuk mengatasi masalah loading
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);
            
            $filename = 'Rekap_Kehadiran_' . ucfirst($jenisRekap) . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            // Fallback ke method alternatif
            return $this->exportToPDFAlternative($jenisRekap, $dataRekap, $dataSiswa, $dataGuru, $tanggalMulai, $tanggalSelesai, $kelas, $status);
        }
    }

    /**
     * Method alternatif untuk export PDF
     */
    private function exportToPDFAlternative($jenisRekap, $dataRekap, $dataSiswa, $dataGuru, $tanggalMulai, $tanggalSelesai, $kelas, $status)
    {
        $statistik = [];
        
        if ($jenisRekap == 'gabungan') {
            $allData = collect($dataSiswa)->merge($dataGuru);
            $statistik = [
                'hadir' => $allData->where('status', 'Hadir')->count(),
                'izin' => $allData->where('status', 'Izin')->count(),
                'sakit' => $allData->where('status', 'Sakit')->count(),
                'alpa' => $allData->whereIn('status', ['Alpa', 'Alpha'])->count(),
            ];
        } else {
            $statistik = [
                'hadir' => $dataRekap->where('status', 'Hadir')->count(),
                'izin' => $dataRekap->where('status', 'Izin')->count(),
                'sakit' => $dataRekap->where('status', 'Sakit')->count(),
                'alpa' => $dataRekap->whereIn('status', ['Alpa', 'Alpha'])->count(),
            ];
        }

        $data = [
            'jenisRekap' => $jenisRekap,
            'dataRekap' => $dataRekap,
            'dataSiswa' => $dataSiswa,
            'dataGuru' => $dataGuru,
            'tanggalMulai' => \Carbon\Carbon::parse($tanggalMulai)->format('d F Y'),
            'tanggalSelesai' => \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y'),
            'kelas' => $kelas,
            'status' => $status,
            'statistik' => $statistik,
            'tanggal_cetak' => now()->format('d F Y H:i:s'),
        ];

        try {
            // Menggunakan app() helper untuk resolve PDF
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.rekap.pdf', $data);
            $pdf->setPaper('A4', 'landscape');
            
            $filename = 'Rekap_Kehadiran_' . ucfirst($jenisRekap) . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('Error generating PDF (alternative): ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Rekap harian untuk dashboard
     */
    public function rekapHarian()
    {
        $tanggal = date('Y-m-d');
        
        $kehadiranSiswa = KehadiranSiswa::with('siswa')
            ->whereDate('tanggal', $tanggal)
            ->get();
            
        $kehadiranGuru = KehadiranGuru::with('guru')
            ->whereDate('tanggal', $tanggal)
            ->get();

        $statistikSiswa = [
            'hadir' => $kehadiranSiswa->where('status', 'Hadir')->count(),
            'izin' => $kehadiranSiswa->where('status', 'Izin')->count(),
            'sakit' => $kehadiranSiswa->where('status', 'Sakit')->count(),
            'alpa' => $kehadiranSiswa->whereIn('status', ['Alpa', 'Alpha'])->count(),
        ];

        $statistikGuru = [
            'hadir' => $kehadiranGuru->where('status', 'Hadir')->count(),
            'izin' => $kehadiranGuru->where('status', 'Izin')->count(),
            'sakit' => $kehadiranGuru->where('status', 'Sakit')->count(),
            'alpa' => $kehadiranGuru->whereIn('status', ['Alpa', 'Alpha'])->count(),
        ];

        return view('admin.rekap.harian', compact(
            'kehadiranSiswa',
            'kehadiranGuru', 
            'statistikSiswa',
            'statistikGuru',
            'tanggal'
        ));
    }

    /**
     * Rekap bulanan untuk laporan periodik
     */
    public function rekapBulanan(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        // Data kehadiran siswa per bulan
        $kehadiranSiswa = KehadiranSiswa::with('siswa')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();
            
        // Data kehadiran guru per bulan
        $kehadiranGuru = KehadiranGuru::with('guru')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Statistik bulanan siswa
        $statistikSiswa = [
            'hadir' => $kehadiranSiswa->where('status', 'Hadir')->count(),
            'izin' => $kehadiranSiswa->where('status', 'Izin')->count(),
            'sakit' => $kehadiranSiswa->where('status', 'Sakit')->count(),
            'alpa' => $kehadiranSiswa->whereIn('status', ['Alpa', 'Alpha'])->count(),
        ];

        // Statistik bulanan guru
        $statistikGuru = [
            'hadir' => $kehadiranGuru->where('status', 'Hadir')->count(),
            'izin' => $kehadiranGuru->where('status', 'Izin')->count(),
            'sakit' => $kehadiranGuru->where('status', 'Sakit')->count(),
            'alpa' => $kehadiranGuru->whereIn('status', ['Alpa', 'Alpha'])->count(),
        ];

        // Rekap per siswa (berapa kali hadir, izin, dll)
        $rekapPerSiswa = $kehadiranSiswa->groupBy('siswa_id')->map(function($items, $siswaId) {
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

        // Rekap per guru
        $rekapPerGuru = $kehadiranGuru->groupBy('id_guru')->map(function($items, $guruId) {
            $guru = $items->first()->guru;
            return [
                'guru' => $guru,
                'hadir' => $items->where('status', 'Hadir')->count(),
                'izin' => $items->where('status', 'Izin')->count(),
                'sakit' => $items->where('status', 'Sakit')->count(),
                'alpa' => $items->whereIn('status', ['Alpa', 'Alpha'])->count(),
                'total' => $items->count(),
            ];
        })->sortBy('guru.nama_guru');

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('admin.rekap.bulanan', compact(
            'kehadiranSiswa',
            'kehadiranGuru',
            'statistikSiswa',
            'statistikGuru',
            'rekapPerSiswa',
            'rekapPerGuru',
            'bulan',
            'tahun',
            'namaBulan'
        ));
    }

    /**
     * API untuk data chart dashboard
     */
    public function chartData(Request $request)
    {
        $periode = $request->get('periode', '7'); // default 7 hari terakhir
        $tanggalMulai = now()->subDays($periode);
        $tanggalSelesai = now();

        // Data kehadiran siswa per hari
        $dataSiswa = KehadiranSiswa::selectRaw('DATE(tanggal) as tanggal, status, COUNT(*) as jumlah')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->groupBy('tanggal', 'status')
            ->get();

        // Data kehadiran guru per hari  
        $dataGuru = KehadiranGuru::selectRaw('DATE(tanggal) as tanggal, status, COUNT(*) as jumlah')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->groupBy('tanggal', 'status')
            ->get();

        // Format data untuk chart
        $chartSiswa = [];
        $chartGuru = [];

        for ($i = $periode - 1; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $dataHari = $dataSiswa->where('tanggal', $tanggal);
            
            $chartSiswa[] = [
                'tanggal' => $tanggal,
                'hadir' => $dataHari->where('status', 'Hadir')->sum('jumlah'),
                'izin' => $dataHari->where('status', 'Izin')->sum('jumlah'),
                'sakit' => $dataHari->where('status', 'Sakit')->sum('jumlah'),
                'alpa' => $dataHari->whereIn('status', ['Alpa', 'Alpha'])->sum('jumlah'),
            ];
        }

        for ($i = $periode - 1; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $dataHari = $dataGuru->where('tanggal', $tanggal);
            
            $chartGuru[] = [
                'tanggal' => $tanggal,
                'hadir' => $dataHari->where('status', 'Hadir')->sum('jumlah'),
                'izin' => $dataHari->where('status', 'Izin')->sum('jumlah'),
                'sakit' => $dataHari->where('status', 'Sakit')->sum('jumlah'),
                'alpa' => $dataHari->whereIn('status', ['Alpa', 'Alpha'])->sum('jumlah'),
            ];
        }

        return response()->json([
            'siswa' => $chartSiswa,
            'guru' => $chartGuru,
        ]);
    }
}