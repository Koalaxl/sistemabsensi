<?php

namespace App\Imports;

use App\Models\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek apakah id_guru atau nip sudah ada, kalau ada skip
        if (Guru::where('id_guru', $row['id_guru'])->exists() || Guru::where('nip', $row['nip'])->exists()) {
            return null;
        }

        return new Guru([
            'nama_guru' => $row['nama_guru'],
            'nip' => $row['nip'],
            'mata_pelajaran' => $row['mata_pelajaran'],
        ]);
    }
}
