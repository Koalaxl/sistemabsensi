@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h2>Edit Jadwal Piket Guru</h2>

    <form action="{{ route('jadwal-piket.update', $jadwalPiketGuru->id_piket) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Guru</label>
        <select name="id_guru" class="form-control" required>
            @foreach($guru as $g)
                <option value="{{ $g->id_guru }}" {{ $g->id_guru == $jadwalPiketGuru->id_guru ? 'selected' : '' }}>
                    {{ $g->nama_guru }}
                </option>
            @endforeach
        </select>
        </div>
        <div class="mb-3">
            <label>Hari</label>
            <input type="text" name="hari" class="form-control" value="{{ $jadwalPiketGuru->hari }}" required>
        </div>
        <div class="mb-3">
            <label>Jam Mulai</label>
            <input type="time" name="jam_mulai" class="form-control" value="{{ $jadwalPiketGuru->jam_mulai }}" required>
        </div>
        <div class="mb-3">
            <label>Jam Selesai</label>
            <input type="time" name="jam_selesai" class="form-control" value="{{ $jadwalPiketGuru->jam_selesai }}" required>
        </div>
        <div class="mb-3">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control">{{ $jadwalPiketGuru->keterangan }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('jadwal-piket.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
