@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0">
                        <i class="bi bi-person-workspace text-primary me-2"></i>
                        Tambah Guru
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

                    <form action="{{ route('admin.guru.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" name="nip" id="nip" 
                                   class="form-control @error('nip') is-invalid @enderror" 
                                   value="{{ old('nip') }}" 
                                   placeholder="Masukkan NIP guru" required>
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nama_guru" class="form-label">Nama Guru</label>
                            <input type="text" name="nama_guru" id="nama_guru" 
                                   class="form-control @error('nama_guru') is-invalid @enderror" 
                                   value="{{ old('nama_guru') }}" 
                                   placeholder="Masukkan nama lengkap guru" required>
                            @error('nama_guru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="mata_pelajaran" class="form-label">Mata Pelajaran</label>
                            <input type="text" name="mata_pelajaran" id="mata_pelajaran" 
                                   class="form-control @error('mata_pelajaran') is-invalid @enderror" 
                                   value="{{ old('mata_pelajaran') }}" 
                                   placeholder="Contoh: Matematika, Bahasa Indonesia" required>
                            @error('mata_pelajaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">
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