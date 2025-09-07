@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-0 text-primary">
                <i class="bi bi-person-lines-fill me-2"></i>
                Data Siswa
            </h3>
            <p class="text-secondary mb-0">Kelola data siswa dengan mudah dan terorganisir.</p>
        </div>
    </div>

    <!-- Action Buttons & Notifications -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary shadow-sm rounded-3">
                <i class="bi bi-plus-circle me-2"></i> Tambah Siswa
            </a>
        </div>
        <div class="col-md-6">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>

    <!-- Filter & Management Card -->
    <div class="card shadow border-0 mb-4 rounded-4">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="mb-0">
                <i class="bi bi-funnel-fill me-2"></i>
                Filter dan Opsi Manajemen
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <!-- Filter Form -->
                <div class="col-lg-6">
                    <form action="{{ route('admin.siswa.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="kelas_filter" class="form-label text-muted">Kelas</label>
                            <select name="kelas" id="kelas_filter" class="form-select rounded-3">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($listKelas as $kelas)
                                    <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>
                                        {{ $kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-grid">
                            <button type="submit" class="btn btn-info rounded-3">
                                <i class="bi bi-funnel me-2"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Conditional Delete by Class -->
                @if(request('kelas'))
                <div class="col-lg-3 d-grid">
                    <form action="{{ route('siswa.destroyByKelas') }}" method="POST"
                        onsubmit="return confirm('Yakin hapus semua siswa di kelas {{ request("kelas") }}?')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="kelas" value="{{ request('kelas') }}">
                        <button type="submit" class="btn btn-danger rounded-3">
                            <i class="bi bi-trash me-2"></i> Hapus Kelas
                        </button>
                    </form>
                </div>
                @endif

                <!-- Search -->
                <form method="GET" action="{{ route('admin.siswa.index') }}" class="mb-3 d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Cari NISN / Nama Siswa" value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(isset($statistik))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $statistik['total'] ?? count($siswa) }}</h4>
                            <p class="mb-0 opacity-8">Total Siswa</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people-fill display-6 opacity-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $statistik['kelas'] ?? count($listKelas) }}</h4>
                            <p class="mb-0 opacity-8">Total Kelas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-house-door-fill display-6 opacity-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $statistik['dengan_ortu'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Dengan No. Ortu</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-phone-fill display-6 opacity-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $statistik['tanpa_ortu'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Tanpa No. Ortu</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-phone-x-fill display-6 opacity-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table Card -->
    <div class="card shadow border-0 rounded-4 mb-4">
        <div class="card-header bg-white rounded-top-4 p-4 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    Daftar Siswa
                    @if(request('kelas'))
                        - Kelas {{ request('kelas') }}
                    @endif
                </h5>
                @if(isset($siswa) && count($siswa) > 0)
                    <small class="text-muted">Total: {{ count($siswa) }} siswa</small>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr class="text-center">
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 60px">No</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">NISN</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Nama Siswa</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Kelas</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">No Orang Tua</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->nisn }}</span>
                            </td>
                            <td>
                                <h6 class="mb-0 fw-semibold">{{ $item->nama_siswa }}</h6>
                            </td>
                            <td>
                                @php
                                    $kelasColors = ['X' => 'bg-info', 'XI' => 'bg-warning text-dark', 'XII' => 'bg-success'];
                                    $kelasClass = 'bg-primary';
                                    foreach($kelasColors as $tingkat => $color) {
                                        if(str_contains($item->kelas, $tingkat)) {
                                            $kelasClass = $color;
                                            break;
                                        }
                                    }
                                @endphp
                                <span class="badge {{ $kelasClass }} py-2 px-3">{{ $item->kelas }}</span>
                            </td>
                            <td>
                                @if($item->no_ortu)
                                    <span class="text-success">
                                        <i class="bi bi-phone me-1"></i>{{ $item->no_ortu }}
                                    </span>
                                @else
                                    <span class="text-muted">
                                        <i class="bi bi-phone-x me-1"></i>-
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.siswa.edit', $item->id) }}" 
                                       class="btn btn-sm btn-warning rounded-3"
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST" 
                                          class="d-inline-block" onsubmit="return confirm('Yakin mau hapus siswa ini?')">
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
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-person-x display-4 mb-3"></i>
                                    <h5>Tidak ada data siswa</h5>
                                    <p class="mb-0">Belum ada siswa yang terdaftar</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bulk Actions & Import -->
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('siswa.hapusSemua') }}" method="POST" class="mb-4"
                onsubmit="return confirm('Yakin hapus semua data siswa?')">
                @csrf 
                @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                    <i class="bi bi-trash3 me-2"></i> Hapus Semua
                </button>
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary rounded-3 me-2">
                <i class="bi bi-arrow-clockwise me-2"></i> Reset Filter
            </a>
        </div>
    </div>

    <!-- Import Card -->
    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-success text-white rounded-top-4 p-4">
            <h5 class="mb-0">
                <i class="bi bi-upload me-2"></i>
                Import Data Siswa
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data" 
                  class="row g-3 align-items-center">
                @csrf
                <div class="col-md-8">
                    <input type="file" name="file" class="form-control rounded-3" required 
                           accept=".xlsx,.xls,.csv">
                </div>
                <div class="col-md-4 d-grid">
                    <button type="submit" class="btn btn-success rounded-3">
                        <i class="bi bi-upload me-2"></i> Import
                    </button>
                </div>
            </form>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Format yang didukung: <strong>.xlsx, .xls, .csv</strong><br>
                    Kolom: NISN, Nama Siswa, Kelas, No Orang Tua
                </small>
                <a href="{{ route('siswa.template') }}" class="btn btn-outline-success rounded-3">
                    <i class="bi bi-download me-2"></i> Download Template
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #0d6efd, #0b5ed7);
}
.bg-gradient-success {
    background: linear-gradient(45deg, #198754, #146c43);
}
.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #d39e00);
}
.bg-gradient-info {
    background: linear-gradient(45deg, #0dcaf0, #087990);
}

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

.card.bg-gradient-primary:hover,
.card.bg-gradient-success:hover,
.card.bg-gradient-warning:hover,
.card.bg-gradient-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
    
    // Auto-submit search form
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }
});
</script>
@endpush