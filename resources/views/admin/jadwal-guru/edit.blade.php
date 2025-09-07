@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h2>Edit Jadwal Guru</h2>

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.jadwal-guru.update', $jadwal->id_jadwal) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="id_guru" class="form-label">Guru</label>
            <select name="id_guru" id="id_guru" class="form-control @error('id_guru') is-invalid @enderror" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}" {{ $jadwal->id_guru == $g->id_guru ? 'selected' : '' }}>
                        {{ $g->nama_guru }}
                    </option>
                @endforeach
            </select>
            @error('id_guru')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="hari" class="form-label">Hari</label>
            <select name="hari" id="hari" class="form-control @error('hari') is-invalid @enderror" required>
                <option value="">-- Pilih Hari --</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}" {{ $jadwal->hari == $h ? 'selected' : '' }}>{{ $h }}</option>
                @endforeach
            </select>
            @error('hari')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mata_pelajaran" class="form-label">Mata Pelajaran</label>
            <input type="text" name="mata_pelajaran" id="mata_pelajaran"
                    value="{{ $jadwal->mata_pelajaran }}"
                   class="form-control @error('mata_pelajaran') is-invalid @enderror" required>
            @error('mata_pelajaran')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jam_mulai" class="form-label">Jam Mulai</label>
            <input type="time" name="jam_mulai" id="jam_mulai"
                   value="{{ $jadwal->jam_mulai }}"
                   class="form-control @error('jam_mulai') is-invalid @enderror" required>
            @error('jam_mulai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="jam_selesai" class="form-label">Jam Selesai</label>
            <input type="time" name="jam_selesai" id="jam_selesai"
                   value="{{ $jadwal->jam_selesai }}"
                   class="form-control @error('jam_selesai') is-invalid @enderror" required>
            @error('jam_selesai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="kelas" class="form-label">Kelas</label>
            <input type="text" name="kelas" id="kelas"
                   value="{{ $jadwal->kelas }}"
                   class="form-control @error('kelas') is-invalid @enderror" required>
            @error('kelas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.jadwal-guru.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
