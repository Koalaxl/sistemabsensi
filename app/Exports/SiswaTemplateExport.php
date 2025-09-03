<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class SiswaTemplateExport implements FromArray, WithHeadings, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            ['📘 TEMPLATE IMPORT DATA SISWA'], // Judul besar
            ['Silakan isi data siswa sesuai format di bawah ini.'],
            ['Kolom "NISN" harus unik. Pastikan tidak ada yang sama.'],
            ['Gunakan format kelas seperti "10-A", "10-B", dst.'],
            [], // Baris kosong sebelum tabel
            ['nisn', 'nama_siswa', 'kelas','no_ortu'], // Header tabel (HARUS sama dgn Import)
        ];
    }

    public function array(): array
    {
        return [
            ['12345', 'Budi Santoso', '10-A', '081234567890'],
            ['67890', 'Siti Aminah', '10-B', '089876543210'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]], // Judul
            2 => ['font' => ['italic' => true, 'color' => ['rgb' => '555555']]],
            3 => ['font' => ['italic' => true, 'color' => ['rgb' => '555555']]],
            4 => ['font' => ['italic' => true, 'color' => ['rgb' => '555555']]],
            6 => [ // Header tabel
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => '1E88E5']
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Merge judul (sampai kolom D karena ada 4 kolom)
                $event->sheet->mergeCells('A1:D1');

                // Auto size semua kolom
                foreach (range('A', 'D') as $col) {
                    $event->sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Border untuk header & contoh data
                $event->sheet->getStyle('A6:D8')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            },
        ];
    }
}
