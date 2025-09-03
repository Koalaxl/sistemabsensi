<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class JadwalGuru extends Model
{
    use HasFactory;

    protected $table = 'jadwal_guru'; // Nama tabel
    protected $primaryKey = 'id_jadwal'; // Primary key yang benar
    public $incrementing = true; // Kalau auto increment
    protected $keyType = 'int'; // Tipe datanya
    protected $fillable = [
        'id_guru', 
        'hari', 
        'mata_pelajaran',
        'jam_mulai', 
        'jam_selesai', 
        'mata_pelajaran',
        'kelas'
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
    
}
