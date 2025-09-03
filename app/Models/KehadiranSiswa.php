<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranSiswa extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_siswa';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
