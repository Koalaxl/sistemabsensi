<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $table = 'agenda';
    protected $primaryKey = 'id_agenda';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'judul',
        'tanggal',
        'mapel',
        'kelas',
        'id_guru',
    ];

    // Relasi ke guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }
}
