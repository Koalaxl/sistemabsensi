<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KehadiranGuru extends Model
{
    use HasFactory;

    protected $table = 'kehadiran_guru';
    protected $primaryKey = 'id_kehadiran_guru'; // primary key sesuai tabel
    public $timestamps = false; // kalau tabel kamu gak ada created_at/updated_at

    protected $fillable = [
        'id_guru',
        'tanggal',
        'status'
    ];

    public function guru()
    {
        // belongsTo(Guru::class, foreignKey, ownerKey)
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }
}
