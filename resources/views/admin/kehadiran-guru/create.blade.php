@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0">
                        <i class="bi bi-person-check text-primary me-2"></i>
                        Tambah Kehadiran Guru
                    </h5>
                </div>
                <div class="card-body p-4">
                    {{-- ✅ Notifikasi sukses --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ❌ Notifikasi gagal --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- ⚠️ Validasi error --}}
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

                    <form action="{{ route('admin.kehadiran-guru.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="id_guru" class="form-label">Guru</label>
                            <select name="id_guru" id="id_guru" 
                                    class="form-select @error('id_guru') is-invalid @enderror" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($guru as $g)
                                    <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>
                                        {{ $g->nama_guru }} - {{ $g->mata_pelajaran }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_guru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" id="tanggal" 
                                   class="form-control @error('tanggal') is-invalid @enderror" 
                                   value="{{ old('tanggal', date('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label">Status Kehadiran</label>
                            <select name="status" id="status" 
                                    class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Hadir" {{ old('status') == 'Hadir' ? 'selected' : '' }}>
                                    ✓ Hadir
                                </option>
                                <option value="Izin" {{ old('status') == 'Izin' ? 'selected' : '' }}>
                                    ⚠ Izin
                                </option>
                                <option value="Sakit" {{ old('status') == 'Sakit' ? 'selected' : '' }}>
                                    🏥 Sakit
                                </option>
                                <option value="Alpha" {{ old('status') == 'Alpha' ? 'selected' : '' }}>
                                    ✗ Alpha
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('admin.kehadiran-guru.index') }}" class="btn btn-secondary">
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