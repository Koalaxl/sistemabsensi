@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Kehadiran Guru</h3>
    <form action="{{ route('admin.kehadiran-guru.update', $kehadiran_guru->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Guru</label>
            <select name="id_guru" class="form-control" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>
                        {{ $g->nama_guru }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ $kehadiran_guru->tanggal }}" required>
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="Hadir" {{ $kehadiran_guru->status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                <option value="Izin" {{ $kehadiran_guru->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Sakit" {{ $kehadiran_guru->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Alpa" {{ $kehadiran_guru->status == 'Alpa' ? 'selected' : '' }}>Alpa</option>
            </select>
        </div>
        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection
