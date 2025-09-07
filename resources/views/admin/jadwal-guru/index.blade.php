@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-0 text-primary">
                <i class="bi bi-calendar2-week-fill me-2"></i>
                Jadwal Guru
            </h3>
            <p class="text-secondary mb-0">Kelola jadwal mengajar guru dengan mudah dan terorganisir.</p>
        </div>
    </div>

    <!-- Action Buttons & Notifications -->
    <div class="row mb-4">
        <div class="col-md-6">
            <a href="{{ route('admin.jadwal-guru.create') }}" class="btn btn-primary shadow-sm rounded-3">
                <i class="bi bi-plus-circle me-2"></i> Tambah Jadwal
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
            <form action="{{ route('admin.jadwal-guru.index') }}" method="GET" class="row g-3 align-items-end">
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
                <div class="col-md-3">
                    <label for="mata_pelajaran" class="form-label text-muted">Mata Pelajaran</label>
                    <select name="mata_pelajaran" id="mata_pelajaran" class="form-select rounded-3">
                        <option value="">-- Semua Mapel --</option>
                        @if(isset($listMataPelajaran))
                            @foreach($listMataPelajaran as $mapel)
                                <option value="{{ $mapel }}" {{ request('mata_pelajaran') == $mapel ? 'selected' : '' }}>
                                    {{ $mapel }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="kelas_filter" class="form-label text-muted">Kelas</label>
                    <select name="kelas_filter" id="kelas_filter" class="form-select rounded-3">
                        <option value="">-- Semua Kelas --</option>
                        @if(isset($listKelas))
                            @foreach($listKelas as $kelas)
                                <option value="{{ $kelas }}" {{ request('kelas_filter') == $kelas ? 'selected' : '' }}>
                                    {{ $kelas }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3 d-grid">
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
                            <h4 class="fw-bold mb-0">{{ $statistik['total'] ?? count($jadwal) }}</h4>
                            <p class="mb-0 opacity-8">Total Jadwal</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-calendar-week display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['guru'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Guru Mengajar</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person-fill display-6 opacity-6"></i>
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
                            <h4 class="fw-bold mb-0">{{ $statistik['kelas'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Kelas Aktif</p>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-house-door display-6 opacity-6"></i>
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
                    Daftar Jadwal Mengajar
                    @if(request('hari'))
                        - Hari {{ request('hari') }}
                    @endif
                </h5>
                @if(isset($jadwal) && count($jadwal) > 0)
                    <small class="text-muted">Total: {{ count($jadwal) }} jadwal</small>
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
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Mata Pelajaran</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Waktu</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7">Kelas</th>
                            <th class="text-uppercase text-secondary fw-bold opacity-7" style="width: 140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $index => $item)
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
                                        default => 'bg-dark'
                                    };
                                @endphp
                                <span class="badge {{ $hariClass }} py-2 px-3">{{ $item->hari }}</span>
                            </td>
                            <td>
                                @php
                                    $mapelColors = [
                                        'Matematika' => 'bg-primary',
                                        'Bahasa Indonesia' => 'bg-success', 
                                        'Bahasa Inggris' => 'bg-info',
                                        'IPA' => 'bg-warning text-dark',
                                        'IPS' => 'bg-danger',
                                        'PKn' => 'bg-secondary'
                                    ];
                                    $mapelClass = $mapelColors[$item->mata_pelajaran] ?? 'bg-dark';
                                @endphp
                                <span class="badge {{ $mapelClass }} py-2 px-3">{{ $item->mata_pelajaran }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</span>
                                    @php
                                        $duration = \Carbon\Carbon::createFromTimeString($item->jam_selesai)
                                                   ->diffInMinutes(\Carbon\Carbon::createFromTimeString($item->jam_mulai));
                                        $hours = floor($duration / 60);
                                        $minutes = $duration % 60;
                                        $durationText = $hours > 0 ? $hours . 'j' . ($minutes > 0 ? ' ' . $minutes . 'm' : '') : $minutes . 'm';
                                    @endphp
                                    <small class="text-muted">{{ $durationText }}</small>
                                </div>
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
                                <span class="badge {{ $kelasClass }} py-2 px-3 fw-bold">{{ $item->kelas }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.jadwal-guru.edit', $item->id_jadwal) }}" 
                                       class="btn btn-sm btn-warning rounded-3"
                                       data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('admin.jadwal-guru.destroy', $item->id_jadwal) }}" 
                                          method="POST" class="d-inline-block" 
                                          onsubmit="return confirm('Yakin mau hapus jadwal ini?')">
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
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-calendar-x display-4 mb-3"></i>
                                    <h5>Tidak ada data jadwal guru</h5>
                                    <p class="mb-0">Belum ada jadwal mengajar yang terdaftar</p>
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
            <form action="{{ route('jadwal-guru.hapusSemua') }}" method="POST" 
                  onsubmit="return confirm('Yakin hapus semua jadwal?')" class="mb-4">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                    <i class="bi bi-trash3 me-2"></i> Hapus Semua
                </button>
            </form>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex justify-content-md-end gap-2">
                <a href="{{ route('admin.jadwal-guru.index') }}" class="btn btn-secondary rounded-3">
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