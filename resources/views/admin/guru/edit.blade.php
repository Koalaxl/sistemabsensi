@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h2>Edit Guru</h2>

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('guru.update', $guru) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nip">NIP</label>
            <input type="text" name="nip" id="nip" class="form-control" 
                   value="{{ old('nip', $guru->nip) }}" required>
            @error('nip')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nama_guru">Nama Guru</label>
            <input type="text" name="nama_guru" id="nama_guru" class="form-control" 
                   value="{{ old('nama_guru', $guru->nama_guru) }}" required>
            @error('nama_guru')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="mata_pelajaran">Mata Pelajaran</label>
            <input type="text" name="mata_pelajaran" id="mata_pelajaran" class="form-control" 
                   value="{{ old('mata_pelajaran', $guru->mata_pelajaran) }}" required>
            @error('mata_pelajaran')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('guru.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
