@extends('layouts.guru.app')

@section('title', 'Absensi Siswa')

@section('content')
<div class="container">
    <h1 class="mb-4">Absensi Siswa</h1>

    {{-- Pilih Kelas --}}
<form action="{{ route('guru.absensi_siswa.index') }}" method="GET" class="mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label for="kelas" class="form-label">Pilih Kelas</label>
            <select name="kelas" id="kelas" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach($listKelas as $k)
                    <option value="{{ $k }}" {{ ($kelasDipilih ?? '') == $k ? 'selected' : '' }}>
                        {{ $k }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </div>
    </div>
</form>

    {{-- Form absensi massal --}}
    @if(!empty($siswa))
    <form action="{{ route('guru.absensi_siswa.store') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas" value="{{ $kelasDipilih }}">
        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>Hadir</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswa as $index => $s)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $s->nama_siswa }}</td>
                    
                    <td>
                        <input type="radio" name="kehadiran[{{ $index }}][status]" value="Hadir">
                    </td>
                    <td>
                        <input type="radio" name="kehadiran[{{ $index }}][status]" value="Izin">
                    </td>
                    <td>
                        <input type="radio" name="kehadiran[{{ $index }}][status]" value="Sakit">
                    </td>
                    
                    <td>
                        <input type="text" name="kehadiran[{{ $index }}][keterangan]" placeholder="Keterangan">
                    </td>

                    <!-- Hidden siswa_id -->
                    <input type="hidden" name="kehadiran[{{ $index }}][siswa_id]" value="{{ $s->id }}">
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit">Simpan Kehadiran</button>
    </form>
    @endif
</div>
@endsection
