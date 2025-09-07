@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-0 text-primary">
                <i class="bi bi-calendar-check-fill me-2"></i>
                Jadwal Piket Guru
            </h3>
            <p class="text-secondary mb-0">Kelola jadwal piket guru dengan mudah dan terorganisir.</p>
        </div>
    </div>

    <!-- Action Buttons & Notifications -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('admin.jadwal-piket.create') }}" class="btn btn-primary shadow-sm rounded-3">
                <i class="bi bi-plus-circle me-2"></i> Tambah Jadwal Piket
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
            <form action="{{ route('admin.jadwal-piket.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="hari_filter" class="form-label text-muted">Hari</label>
                    <select name="hari" id="hari_filter" class="form-select rounded-3">
                        <option value="">-- Semua Hari --</option>
                        <option value="Senin" {{ request('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                        <option value="Selasa" {{ request('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                        <option value="Rabu" {{ request('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                        <option value="Kamis" {{ request('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                        <option value="Jumat" {{ request('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                        <option value="Sabtu" {{ request('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label text-muted">Cari Guru</label>
                    <input type="text" name="search" id="search" class="form-control rounded-3" 
                           placeholder="Nama guru..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="jam_filter" class="form-label text-muted">Waktu</label>
                    <select name="jam" id="jam_filter" class="form-select rounded-3">
                        <option value="">-- Semua Waktu --</option>
                        <option value="pagi" {{ request('jam') == 'pagi' ? 'selected' : '' }}>Pagi (06:00-12:00)</option>
                        <option value="siang" {{ request('jam') == 'siang' ? 'selected' : '' }}>Siang (12:00-18:00)</option>
                        <option value="sore" {{ request('jam') == 'sore' ? 'selected' : '' }}>Sore (18:00-24:00)</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-info rounded-3">
                        <i class="bi bi-search me-2"></i> Filter
                    </button>
                </div>
            </form>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['total'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Total Piket</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-check display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['aktif'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Piket Hari Ini</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-check display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['guru'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Guru Piket</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">7</h4>
                            <p class="mb-0 opacity-8">Hari/Minggu</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-week display-6 opacity-6"></i>
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
                    Daftar Jadwal Piket
                </h5>
                @if(isset($jadwalPiket) && count($jadwalPiket) > 0)
                    <small class="text-muted">Total: {{ count($jadwalPiket) }} jadwal piket</small>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height:600px; overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light sticky-top">
                        <tr class="text-center">
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 60px">No</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Guru</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Hari</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Waktu</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Keterangan</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwalPiket as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <h6 class="mb-0 fw-semibold">{{ $item->guru->nama_guru }}</h6>
                                <small class="text-muted">NIP: {{ $item->guru->nip ?? '-' }}</small>
                            </td>
                            <td>
                                @php
                                    $hariClass = match($item->hari) {
                                        'Senin' => 'bg-primary',
                                        'Selasa' => 'bg-success',
                                        'Rabu' => 'bg-warning text-dark',
                                        'Kamis' => 'bg-info',
                                        'Jumat' => 'bg-danger',
                                        'Sabtu' => 'bg-secondary',
                                        'Minggu' => 'bg-dark',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $hariClass }} py-2 px-3">{{ $item->hari }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</span>
                                    @php
                                        $duration = \Carbon\Carbon::createFromTimeString($item->jam_selesai)
                                                   ->diffInHours(\Carbon\Carbon::createFromTimeString($item->jam_mulai));
                                    @endphp
                                    <small class="text-muted">{{ $duration }} jam</small>
                                </div>
                            </td>
                            <td>
                                {{ $item->keterangan ?? '-' }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.jadwal-piket.edit', $item->id_piket) }}" 
                                       class="btn btn-sm btn-warning rounded-3"
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.jadwal-piket.destroy', $item->id_piket) }}" 
                                          method="POST" class="d-inline-block" 
                                          onsubmit="return confirm('Yakin mau hapus jadwal piket ini?')">
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
                                    <i class="bi bi-calendar-x display-4 mb-3"></i>
                                    <h5>Tidak ada data jadwal piket</h5>
                                    <p class="mb-0">Belum ada jadwal piket guru yang terdaftar</p>
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
            <form action="{{ route('jadwal-piket.hapusSemua') }}" method="POST" 
                  onsubmit="return confirm('Yakin hapus semua jadwal piket?')" class="mb-4">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                    <i class="bi bi-trash3 me-2"></i> Hapus Semua
                </button>
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex justify-content-md-end gap-2">
                <a href="{{ route('admin.jadwal-piket.index') }}" class="btn btn-secondary rounded-3">
                    <i class="bi bi-arrow-clockwise me-2"></i> Reset Filter
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

.badge {
    transition: all 0.2s ease;
}

.sticky-top {
    top: 0;
    z-index: 10;
}

.card.bg-gradient-primary:hover,
.card.bg-gradient-success:hover,
.card.bg-gradient-warning:hover,
.card.bg-gradient-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
    
    // Highlight current day
    const currentDay = new Date().toLocaleDateString('id-ID', { weekday: 'long' });
    const dayBadges = document.querySelectorAll('.badge');
    dayBadges.forEach(badge => {
        if (badge.textContent.trim() === currentDay) {
            badge.style.animation = 'pulse 2s infinite';
        }
    });
});

// Add pulse animation for current day
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);
</script>
@endpush