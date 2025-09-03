@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus text-primary me-2"></i>
                        Tambah Siswa
                    </h5>
                </div>
                <div class="card-body p-4">
                    {{-- Notifikasi error global --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('siswa.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input type="text" name="nisn" id="nisn" 
                                   class="form-control @error('nisn') is-invalid @enderror" 
                                   value="{{ old('nisn') }}" 
                                   placeholder="Masukkan NISN siswa" required>
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nama_siswa" class="form-label">Nama Siswa</label>
                            <input type="text" name="nama_siswa" id="nama_siswa" 
                                   class="form-control @error('nama_siswa') is-invalid @enderror" 
                                   value="{{ old('nama_siswa') }}" 
                                   placeholder="Masukkan nama lengkap siswa" required>
                            @error('nama_siswa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="kelas" class="form-label">Kelas</label>
                            <input type="text" name="kelas" id="kelas" 
                                   class="form-control @error('kelas') is-invalid @enderror" 
                                   value="{{ old('kelas') }}" 
                                   placeholder="Contoh: XII IPA 1" required>
                            @error('kelas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="no_ortu" class="form-label">Nomor HP Orang Tua</label>
                            <input type="text" name="no_ortu" id="no_ortu" 
                                   class="form-control @error('no_ortu') is-invalid @enderror" 
                                   value="{{ old('no_ortu') }}" 
                                   placeholder="Contoh: 081234567890" required>
                            @error('no_ortu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('siswa.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection