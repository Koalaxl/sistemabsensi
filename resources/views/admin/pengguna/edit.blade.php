@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h2>Edit Pengguna</h2>

    <form action="{{ route('pengguna.update', $pengguna->id_pengguna) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="id_guru" class="form-label">Pilih Guru</label>
            <select name="id_guru" id="id_guru" class="form-select" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id_guru }}" {{ $pengguna->id_guru == $g->id_guru ? 'selected' : '' }}>
                        {{ $g->nama_guru }} ({{ $g->nip }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" value="{{ $pengguna->username }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-select" required>
                <option value="admin" {{ $pengguna->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="guru" {{ $pengguna->role == 'guru' ? 'selected' : '' }}>Guru</option>
                <option value="guru_piket" {{ $pengguna->role == 'guru_piket' ? 'selected' : '' }}>Guru Piket</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('pengguna.index') }}" class="btn btn-secondary">Batal</a>
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