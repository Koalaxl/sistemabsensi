<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Siswa([
            'nisn'       => $row['nisn'] ?? null,
            'nama_siswa' => $row['nama_siswa'] ?? null,
            'kelas'      => $row['kelas'] ?? null,
            'no_ortu'    => $row['no_ortu'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nisn'       => 'required',
            'nama_siswa' => 'required',
            'kelas'      => 'required',
            'no_ortu'    => 'required|numeric',
        ];
    }
}