@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h2>Tambah Pengguna</h2>

    {{-- Notifikasi error global --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pengguna.store') }}" method="POST">
        @csrf
        
        <div class="mb-3">
            <label for="id_guru" class="form-label">Pilih Guru</label>
            <select name="id_guru" id="id_guru" class="form-select @error('id_guru') is-invalid @enderror" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}" {{ old('id_guru') == $g->id_guru ? 'selected' : '' }}>
                        {{ $g->nama_guru }} ({{ $g->nip }})
                    </option>
                @endforeach
            </select>
            @error('id_guru')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="guru_piket" {{ old('role') == 'guru_piket' ? 'selected' : '' }}>Guru Piket</option>
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#id_guru').select2({
            placeholder: "Cari nama guru...",
            allowClear: true
        });
    });
</script>
@endpush