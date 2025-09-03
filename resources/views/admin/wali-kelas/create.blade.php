@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h2>Tambah Wali Kelas</h2>

    {{-- Notifikasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('wali-kelas.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Guru</label>
            <select name="guru_id" class="form-select" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}">{{ $g->nama_guru }} - {{ $g->mata_pelajaran }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Kelas</label>
            <input type="text" name="kelas" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
