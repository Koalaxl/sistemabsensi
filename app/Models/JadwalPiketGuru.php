<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPiketGuru extends Model
{
    protected $table = 'jadwal_piket_guru'; // sesuaikan dengan nama tabel
    protected $primaryKey = 'id_piket';     // <── tambahkan ini
    public $incrementing = true;
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id_guru',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }
}