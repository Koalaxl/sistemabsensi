@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-0 text-primary">
                <i class="bi bi-person-check-fill me-2"></i>
                Data Kehadiran Siswa
            </h3>
            <p class="text-secondary mb-0">Kelola data kehadiran siswa dengan mudah dan akurat.</p>
        </div>
    </div>

    <!-- Action Buttons & Notifications -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('admin.kehadiran-siswa.create') }}" class="btn btn-primary shadow-sm rounded-3">
                <i class="bi bi-plus-circle me-2"></i> Tambah Kehadiran
            </a>
            <a href="{{ route('admin.kehadiran-siswa.export') }}" class="btn btn-success shadow-sm rounded-3 ms-2">
                <i class="bi bi-file-earmark-excel-fill me-2"></i> Export Excel
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

    <!-- Filter Card -->
    <div class="card shadow border-0 mb-4 rounded-4">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="mb-0">
                <i class="bi bi-funnel-fill me-2"></i>
                Filter Data Kehadiran
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.kehadiran-siswa.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="tanggal_filter" class="form-label text-muted">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal_filter" class="form-control rounded-3" 
                           value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label for="status_filter" class="form-label text-muted">Status</label>
                    <select name="status" id="status_filter" class="form-select rounded-3">
                        <option value="">Semua Status</option>
                        @foreach($listStatus as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="kelas_filter" class="form-label text-muted">Kelas</label>
                    <select name="kelas" id="kelas_filter" class="form-select rounded-3">
                        <option value="">Semua Kelas</option>
                        @if(isset($listKelas))
                            @foreach($listKelas as $kelas)
                                <option value="{{ $kelas }}" {{ request('kelas') == $kelas ? 'selected' : '' }}>
                                    {{ $kelas }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-info rounded-3">
                        <i class="bi bi-funnel me-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if(isset($statistik))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $statistik['hadir'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Hadir</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-check-circle-fill display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['izin'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Izin</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-exclamation-circle-fill display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['sakit'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Sakit</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-bandaid-fill display-6 opacity-6"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-danger text-white rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $statistik['alpa'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Alpa</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-x-circle-fill display-6 opacity-6"></i>
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
                    Data Kehadiran Siswa
                    @if(request('tanggal'))
                        - {{ \Carbon\Carbon::parse(request('tanggal'))->format('d M Y') }}
                    @endif
                </h5>
                @if(isset($kehadiran) && count($kehadiran) > 0)
                    <small class="text-muted">Total: {{ count($kehadiran) }} data kehadiran</small>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr class="text-center">
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 60px">No</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Siswa</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Tanggal</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Status</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Keterangan</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kehadiran as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <h6 class="mb-0 fw-semibold">{{ $item->siswa->nama_siswa }}</h6>
                                <small class="text-muted">{{ $item->siswa->kelas ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</span>
                                <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal)->format('l') }}</small>
                            </td>
                            <td>
                                @php
                                    $statusClass = match(strtolower($item->status)) {
                                        'hadir' => 'bg-success text-white',
                                        'izin' => 'bg-warning text-dark',
                                        'sakit' => 'bg-info text-white',
                                        'alpa' => 'bg-danger text-white',
                                        default => 'bg-secondary text-white'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }} py-2 px-3 fw-bold">
                                    {{ ucfirst(strtolower($item->status)) }}
                                </span>
                            </td>
                            <td>
                                {{ $item->keterangan ?? '-' }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.kehadiran-siswa.edit', $item->id) }}" 
                                       class="btn btn-sm btn-primary rounded-3"
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.kehadiran-siswa.destroy', $item->id) }}" 
                                          method="POST" class="d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin hapus data ini?')" 
                                                class="btn btn-sm btn-danger rounded-3"
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
                                    <h5>Tidak ada data kehadiran siswa</h5>
                                    <p class="mb-0">Belum ada data kehadiran yang tercatat</p>
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
            <form action="{{ route('kehadiran-siswa.hapusSemua') }}" method="POST" 
                  onsubmit="return confirm('Yakin hapus semua data kehadiran?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                    <i class="bi bi-trash3 me-2"></i> Hapus Semua
                </button>
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.kehadiran-siswa.index') }}" class="btn btn-secondary rounded-3">
                <i class="bi bi-arrow-clockwise me-2"></i> Reset Filter
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-success {
    background: linear-gradient(45deg, #198754, #146c43);
}
.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #d39e00);
}
.bg-gradient-info {
    background: linear-gradient(45deg, #0dcaf0, #087990);
}
.bg-gradient-danger {
    background: linear-gradient(45deg, #dc3545, #a02834);
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

.card.bg-gradient-success:hover,
.card.bg-gradient-warning:hover,
.card.bg-gradient-info:hover,
.card.bg-gradient-danger:hover {
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
    
    // Set default date to today if not set
    const tanggalInput = document.getElementById('tanggal_filter');
    if (tanggalInput && !tanggalInput.value) {
        tanggalInput.value = new Date().toISOString().split('T')[0];
    }
});
</script>
@endpush