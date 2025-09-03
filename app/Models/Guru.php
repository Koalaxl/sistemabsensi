<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guru extends Authenticatable
{
    use Notifiable;

    protected $table = 'guru';
    protected $primaryKey = 'id_guru';
    public $incrementing = true;   // kalau auto increment
    protected $keyType = 'int';    // tipe data primary key

    protected $fillable = [
        'nama_guru',
        'nip',
        'mata_pelajaran'
    ];
    // app/Models/User.php
    public function guru()
    {
        return $this->hasOne(Guru::class, 'id_guru', 'id_guru');
    }

}