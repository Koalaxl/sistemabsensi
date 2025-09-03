<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa'; // nama tabel di database

    protected $fillable = [
        'nisn',
        'nama_siswa',
        'kelas',
        'no_ortu',
    ];
}
