@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h2>Edit Siswa</h2>
    <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>NISN</label>
            <input type="text" name="nisn" class="form-control" value="{{ $siswa->nisn }}" required>
        </div>
        <div class="mb-3">
            <label>Nama Siswa</label>
            <input type="text" name="nama_siswa" class="form-control" value="{{ $siswa->nama_siswa }}" required>
        </div>
        <div class="mb-3">
            <label>Kelas</label>
            <input type="text" name="kelas" class="form-control" value="{{ $siswa->kelas }}" required>
        </div>
        <div class="mb-3">
            <label>Nomor Orang Tua</label>
            <input type="text" name="no_ortu" class="form-control" value="{{ $siswa->no_ortu }}" required>
        </div>
        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
