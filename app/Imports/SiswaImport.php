<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;


class SiswaImport implements ToModel, WithHeadingRow
{
public function model(array $row)
    {
        Log::info($row); // Debug hasil import

        // Pastikan ada key nisn
        if (!isset($row['nisn'])) {
            return null; // skip jika kolom nisn tidak ada
        }

        if (Siswa::where('nisn', $row['nisn'])->exists()) {
            return null;
        }

        return new Siswa([
            'nisn'       => $row['nisn'],
            'nama_siswa' => $row['nama_siswa'],
            'kelas'      => $row['kelas'],
            'no_ortu'    => $row['no_ortu'],
        ]);
    }
}
