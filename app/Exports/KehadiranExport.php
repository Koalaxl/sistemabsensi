<?php
// app/Exports/KehadiranExport.php
namespace App\Exports;

use App\Models\Kehadiran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KehadiranExport implements FromCollection, WithHeadings
{
    protected $kelas;
    protected $tanggal;

    public function __construct($kelas=null, $tanggal=null)
    {
        $this->kelas = $kelas;
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        $query = Kehadiran::with('siswa');

        if($this->kelas) $query->whereHas('siswa', fn($q)=> $q->where('kelas', $this->kelas));
        if($this->tanggal) $query->where('tanggal', $this->tanggal);

        return $query->get()->map(function($k){
            return [
                'NISN' => $k->nisn,
                'Nama Siswa' => $k->siswa->nama_siswa,
                'Kelas' => $k->siswa->kelas,
                'Tanggal' => $k->tanggal,
                'Status' => $k->status
            ];
        });
    }

    public function headings(): array
    {
        return ['NISN','Nama Siswa','Kelas','Tanggal','Status'];
    }
}
