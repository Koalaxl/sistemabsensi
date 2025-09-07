<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

class SiswaTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        // Header WAJIB sama dengan SiswaImport
        return [
            'nisn',
            'nama_siswa',
            'kelas',
            'no_ortu',
        ];
    }

    public function array(): array
    {
        // Contoh baris dummy biar user tau format
        return [
            ['1234567890', 'Budi Santoso', 'X IPA 1', '081234567890'],
            ['0987654321', 'Ani Lestari', 'XI IPS 2', '089876543210'],
        ];
    }
}
