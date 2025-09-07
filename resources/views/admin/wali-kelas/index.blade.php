@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-0 text-primary">
                <i class="bi bi-person-badge-fill me-2"></i>
                Data Wali Kelas
            </h3>
            <p class="text-secondary mb-0">Kelola data wali kelas secara terstruktur dan efisien.</p>
        </div>
    </div>

    <!-- Action Buttons & Notifications -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('admin.wali-kelas.create') }}" class="btn btn-primary shadow-sm rounded-3">
                <i class="bi bi-plus-circle me-2"></i> Tambah Wali Kelas
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
                    <form action="{{ route('admin.wali-kelas.index') }}" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="kelas_filter" class="form-label text-muted">Kelas</label>
                            <select name="kelas_filter" id="kelas_filter" class="form-select rounded-3">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($listKelas as $kelas)
                                    <option value="{{ $kelas }}" {{ request('kelas_filter') == $kelas ? 'selected' : '' }}>
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

                <!-- Search -->
                <div class="col-lg-3">
                    <form action="{{ route('admin.wali-kelas.index') }}" method="GET" class="d-grid">
                        <input type="text" name="search" class="form-control rounded-3"
                               placeholder="Cari guru atau kelas..." value="{{ request('search') }}">
                        <input type="hidden" name="kelas_filter" value="{{ request('kelas_filter') }}">
                    </form>
                </div>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['total'] ?? count($waliKelas) }}</h4>
                            <p class="mb-0 opacity-8">Total Wali Kelas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-badge display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['kelas'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Kelas Terawasi</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-house-door display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['mapel'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Mata Pelajaran</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-book display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['aktif'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Guru Aktif</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check display-6 opacity-6"></i>
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
                    Daftar Wali Kelas
                    @if(request('kelas_filter'))
                        - Kelas {{ request('kelas_filter') }}
                    @endif
                </h5>
                @if(isset($waliKelas) && count($waliKelas) > 0)
                    <small class="text-muted">Total: {{ count($waliKelas) }} wali kelas</small>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr class="text-center">
                            <th style="width:60px">No</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th style="width:140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($waliKelas as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td><h6 class="mb-0 fw-semibold">{{ $item->guru->nama_guru }}</h6></td>
                            <td><span class="badge bg-light text-dark border">{{ $item->guru->nip }}</span></td>
                            <td>
                                <span class="badge bg-dark py-2 px-3">{{ $item->guru->mata_pelajaran }}</span>
                            </td>
                            <td>
                                @php
                                    $kelasColors = ['X' => 'bg-info', 'XI' => 'bg-warning text-dark', 'XII' => 'bg-success'];
                                    $kelasClass = 'bg-primary';
                                    foreach($kelasColors as $tingkat => $color) {
                                        if(str_contains($item->kelas, $tingkat)) {
                                            $kelasClass = $color; break;
                                        }
                                    }
                                @endphp
                                <span class="badge {{ $kelasClass }} py-2 px-3 fw-bold">{{ $item->kelas }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.wali-kelas.edit', $item->id) }}" 
                                       class="btn btn-sm btn-warning rounded-3" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.wali-kelas.destroy', $item->id) }}" 
                                          method="POST" class="d-inline-block"
                                          onsubmit="return confirm('Yakin mau hapus wali kelas ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger rounded-3" data-bs-toggle="tooltip" title="Hapus">
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
                                    <h5>Tidak ada data wali kelas</h5>
                                    <p class="mb-0">Belum ada wali kelas yang terdaftar</p>
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
            <form action="{{ route('wali-kelas.hapusSemua') }}" method="POST" class="mb-4"
                onsubmit="return confirm('Yakin hapus semua data wali kelas?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                    <i class="bi bi-trash3 me-2"></i> Hapus Semua
                </button>
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.wali-kelas.index') }}" class="btn btn-secondary rounded-3">
                <i class="bi bi-arrow-clockwise me-2"></i> Reset Filter
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-primary {background: linear-gradient(45deg,#0d6efd,#0b5ed7);}
.bg-gradient-success {background: linear-gradient(45deg,#198754,#146c43);}
.bg-gradient-warning {background: linear-gradient(45deg,#ffc107,#d39e00);}
.bg-gradient-info {background: linear-gradient(45deg,#0dcaf0,#087990);}
.table tbody tr:hover {background:#f8f9fa;transition:all .3s;}
.btn {transition:all .2s;}
.btn:hover {transform:translateY(-1px);}
.card {transition:all .3s;}
.card.bg-gradient-primary:hover,
.card.bg-gradient-success:hover,
.card.bg-gradient-warning:hover,
.card.bg-gradient-info:hover {
    transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,0,0,.15);
}
.sticky-top {top:0;z-index:10;}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList=[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(el){return new bootstrap.Tooltip(el);});
    const searchInput=document.querySelector('input[name="search"]');
    if(searchInput){
        let searchTimeout;
        searchInput.addEventListener('input',function(){
            clearTimeout(searchTimeout);
            searchTimeout=setTimeout(()=>{this.form.submit();},500);
        });
    }
});
</script>
@endpush
