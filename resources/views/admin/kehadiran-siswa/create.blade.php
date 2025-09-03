@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar-check text-primary me-2"></i>
                        Tambah Kehadiran Siswa
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

                    <form action="{{ route('admin.kehadiran-siswa.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="siswa_id" class="form-label">Siswa</label>
                            <select name="siswa_id" id="siswa_id" 
                                    class="form-select @error('siswa_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($siswa as $s)
                                    <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_siswa }} - {{ $s->kelas }}
                                    </option>
                                @endforeach
                            </select>
                            @error('siswa_id')
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

                        <div class="mb-3">
                            <label for="status" class="form-label">Status Kehadiran</label>
                            <select name="status" id="status" 
                                    class="form-select @error('status') is-invalid @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Hadir" {{ old('status') == 'Hadir' ? 'selected' : '' }}>
                                    <i class="text-success">✓</i> Hadir
                                </option>
                                <option value="Izin" {{ old('status') == 'Izin' ? 'selected' : '' }}>
                                    <i class="text-warning">⚠</i> Izin
                                </option>
                                <option value="Sakit" {{ old('status') == 'Sakit' ? 'selected' : '' }}>
                                    <i class="text-info">🏥</i> Sakit
                                </option>
                                <option value="Alpa" {{ old('status') == 'Alpa' ? 'selected' : '' }}>
                                    <i class="text-danger">✗</i> Alpa
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="keterangan" class="form-label">Keterangan <small class="text-muted">(opsional)</small></label>
                            <textarea name="keterangan" id="keterangan" 
                                      class="form-control @error('keterangan') is-invalid @enderror" 
                                      rows="3" 
                                      placeholder="Tambahkan keterangan jika diperlukan">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>
                            <a href="{{ route('admin.kehadiran-siswa.index') }}" class="btn btn-secondary">
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