@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-0 text-primary">
                <i class="bi bi-people-fill me-2"></i>
                Data Pengguna
            </h3>
            <p class="text-secondary mb-0">Kelola data akun pengguna sistem dengan mudah.</p>
        </div>
    </div>

    <!-- Action Buttons & Notifications -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('admin.pengguna.create') }}" class="btn btn-primary shadow-sm rounded-3">
                <i class="bi bi-plus-circle me-2"></i> Tambah Pengguna
            </a>
        </div>
        <div class="col-md-6">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card shadow border-0 mb-4 rounded-4">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="mb-0">
                <i class="bi bi-funnel-fill me-2"></i>
                Filter dan Pencarian
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.pengguna.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="role_filter" class="form-label text-muted">Role</label>
                    <select name="role" id="role_filter" class="form-select rounded-3">
                        <option value="">-- Semua Role --</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label text-muted">Cari Pengguna</label>
                    <input type="text" name="search" id="search" class="form-control rounded-3" 
                           placeholder="Nama atau username..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-grid">
                    <button type="submit" class="btn btn-info rounded-3">
                        <i class="bi bi-search me-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow border-0 rounded-4 mb-4">
        <div class="card-header bg-white rounded-top-4 p-4 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    Daftar Pengguna
                </h5>
                @if(isset($pengguna) && count($pengguna) > 0)
                    <small class="text-muted">Total: {{ count($pengguna) }} pengguna</small>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr class="text-center">
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 60px">No</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Nama Pengguna</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Username</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Role</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengguna as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <h6 class="mb-0 fw-semibold">{{ $item->nama_pengguna }}</h6>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->username }}</span>
                            </td>
                            <td>
                                @php
                                    $roleClass = match(strtolower($item->role)) {
                                        'admin' => 'bg-danger',
                                        'guru' => 'bg-warning text-dark',
                                        'siswa' => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $roleClass }} py-2 px-3">{{ ucfirst($item->role) }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.pengguna.edit', $item->id_pengguna) }}" 
                                       class="btn btn-sm btn-warning rounded-3" 
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.pengguna.destroy', $item->id_pengguna) }}" 
                                          method="POST" class="d-inline-block" 
                                          onsubmit="return confirm('Yakin mau hapus pengguna ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-3"
                                                data-bs-toggle="tooltip" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people-fill display-4 mb-3"></i>
                                    <h5>Tidak ada data pengguna</h5>
                                    <p class="mb-0">Belum ada pengguna yang terdaftar dalam sistem</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('admin.pengguna.hapusSemua') }}" method="POST" 
                  onsubmit="return confirm('Yakin hapus semua data pengguna?')" class="mb-4">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                    <i class="bi bi-trash3 me-2"></i> Hapus Semua
                </button>
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary rounded-3">
                <i class="bi bi-arrow-clockwise me-2"></i> Reset Filter
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table tbody tr:hover {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

.card {
    transition: all 0.3s ease;
}

.badge {
    transition: all 0.2s ease;
}

.sticky-top {
    top: 0;
    z-index: 10;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush