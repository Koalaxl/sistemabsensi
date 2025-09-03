@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0 text-primary">
                        <i class="bi bi-bar-chart-line-fill me-2"></i>
                        Rekap Kehadiran
                    </h3>
                    <p class="text-secondary mb-0">Laporan kehadiran siswa dan guru yang dapat diekspor ke Excel</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#infoModal">
                        <i class="bi bi-info-circle"></i> Info
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter & Export Card -->
    <div class="card shadow border-0 mb-4 rounded-4">
        <div class="card-header bg-gradient-primary text-white rounded-top-4 p-4">
            <h5 class="mb-0">
                <i class="bi bi-funnel-fill me-2"></i>
                Filter & Export Data
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.rekap.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Pilih Jenis Rekap -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted">
                            <i class="bi bi-card-checklist me-1"></i>
                            Jenis Rekap
                        </label>
                        <select name="jenis_rekap" class="form-select rounded-3" id="jenisRekap" required>
                            <option value="">Pilih Jenis Rekap</option>
                            <option value="siswa" {{ request('jenis_rekap') == 'siswa' ? 'selected' : '' }}>
                                Kehadiran Siswa
                            </option>
                            <option value="guru" {{ request('jenis_rekap') == 'guru' ? 'selected' : '' }}>
                                Kehadiran Guru
                            </option>
                            <option value="gabungan" {{ request('jenis_rekap') == 'gabungan' ? 'selected' : '' }}>
                                Rekap Gabungan
                            </option>
                        </select>
                    </div>

                    <!-- Tanggal Mulai -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted">
                            <i class="bi bi-calendar-date me-1"></i>
                            Tanggal Mulai
                        </label>
                        <input type="date" name="tanggal_mulai" class="form-control rounded-3" 
                               value="{{ request('tanggal_mulai') }}" required>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted">
                            <i class="bi bi-calendar-check me-1"></i>
                            Tanggal Selesai
                        </label>
                        <input type="date" name="tanggal_selesai" class="form-control rounded-3" 
                               value="{{ request('tanggal_selesai') }}" required>
                    </div>

                    <!-- Filter Kelas (untuk siswa) -->
                    <div class="col-md-3" id="filterKelas" style="{{ request('jenis_rekap') == 'siswa' ? '' : 'display: none;' }}">
                        <label class="form-label fw-semibold text-muted">
                            <i class="bi bi-house-door me-1"></i>
                            Kelas
                        </label>
                        <select name="kelas" class="form-select rounded-3">
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

                    <!-- Filter Status -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted">
                            <i class="bi bi-check-circle me-1"></i>
                            Status
                        </label>
                        <select name="status" class="form-select rounded-3">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpa" {{ request('status') == 'alpa' ? 'selected' : '' }}>Alpa</option>
                        </select>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="col-md-12">
                        <div class="d-flex gap-3 flex-wrap"> <!-- ubah gap-2 jadi gap-3 -->
                            <button type="submit" class="btn btn-primary rounded-3 px-4">
                                <i class="bi bi-search me-2"></i>
                                Tampilkan Rekap
                            </button>
                            
                            <button type="button" class="btn btn-success rounded-3 px-4" id="exportExcel">
                                <i class="bi bi-file-earmark-excel-fill me-2"></i>
                                Export Excel
                            </button>
                            
                            <button type="button" class="btn btn-info rounded-3 px-4" id="exportPDF">
                                <i class="bi bi-file-earmark-pdf-fill me-2"></i>
                                Export PDF
                            </button>
                            
                            <button type="button" class="btn btn-warning rounded-3 px-4" onclick="printReport()">
                                <i class="bi bi-printer-fill me-2"></i>
                                Print
                            </button>
                            
                            <a href="{{ route('admin.rekap.index') }}" class="btn btn-secondary rounded-3 px-4">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Reset Filter
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistik Card -->
    @if(isset($statistik))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white rounded-4 shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="fw-bold mb-0">{{ $statistik['hadir'] ?? 0 }}</h4>
                            <p class="mb-0 opacity-8">Total Hadir</p>
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
                            <p class="mb-0 opacity-8">Total Izin</p>
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
                            <p class="mb-0 opacity-8">Total Sakit</p>
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
                            <p class="mb-0 opacity-8">Total Alpa</p>
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

    <!-- Tabel Rekap -->
    <div class="card shadow border-0 rounded-4">
        <div class="card-header bg-white rounded-top-4 p-4 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-table me-2"></i>
                    Data Rekap Kehadiran
                    @if(request('jenis_rekap'))
                        - {{ ucfirst(str_replace('_', ' ', request('jenis_rekap'))) }}
                    @endif
                </h5>
                @if(isset($dataRekap) && count($dataRekap) > 0)
                    <small class="text-muted">
                        Total: {{ count($dataRekap) }} data
                        @if(request('tanggal_mulai') && request('tanggal_selesai'))
                            | Periode: {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse(request('tanggal_selesai'))->format('d M Y') }}
                        @endif
                    </small>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="rekapTable">
                    @if(isset($dataRekap) && count($dataRekap) > 0)
                        @if(request('jenis_rekap') == 'siswa')
                            <!-- Header untuk Siswa -->
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7 px-4">No</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Nama Siswa</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Kelas</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Tanggal</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataRekap as $index => $item)
                                <tr>
                                    <td class="px-4">{{ $index + 1 }}</td>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">{{ $item->siswa->nama_siswa }}</h6>
                                        <small class="text-muted">NIS: {{ $item->siswa->nis ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $item->siswa->kelas ?? '-' }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match(strtolower($item->status)) {
                                                'hadir' => 'bg-success',
                                                'izin' => 'bg-warning text-dark',
                                                'sakit' => 'bg-info',
                                                'alpa' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} py-2 px-3">{{ $item->status }}</span>
                                    </td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        @elseif(request('jenis_rekap') == 'guru')
                            <!-- Header untuk Guru -->
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7 px-4">No</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Nama Guru</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Mata Pelajaran</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Tanggal</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary fw-bold opacity-7">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataRekap as $index => $item)
                                <tr>
                                    <td class="px-4">{{ $index + 1 }}</td>
                                    <td>
                                        <h6 class="mb-0 fw-semibold">{{ $item->guru->nama_guru }}</h6>
                                        <small class="text-muted">NIP: {{ $item->guru->nip ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $item->guru->mata_pelajaran ?? '-' }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match(strtolower($item->status)) {
                                                'hadir' => 'bg-success',
                                                'izin' => 'bg-warning text-dark',
                                                'sakit' => 'bg-info',
                                                'alpa' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }} py-2 px-3">{{ $item->status }}</span>
                                    </td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        @endif
                    @else
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-search display-4 mb-3"></i>
                                        <h5>Tidak ada data yang ditemukan</h5>
                                        <p class="mb-0">Silakan sesuaikan filter pencarian Anda</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Info -->
<div class="modal fade" id="infoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4">
            <div class="modal-header bg-info text-white rounded-top-4">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    Informasi Rekap Kehadiran
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold">Jenis Rekap:</h6>
                <ul class="list-unstyled ms-3">
                    <li><i class="bi bi-dot text-primary"></i> <strong>Kehadiran Siswa:</strong> Laporan kehadiran semua siswa</li>
                    <li><i class="bi bi-dot text-primary"></i> <strong>Kehadiran Guru:</strong> Laporan kehadiran semua guru</li>
                    <li><i class="bi bi-dot text-primary"></i> <strong>Rekap Gabungan:</strong> Laporan kehadiran siswa dan guru dalam satu file</li>
                </ul>
                
                <h6 class="fw-bold mt-3">Status Kehadiran:</h6>
                <div class="row">
                    <div class="col-6">
                        <span class="badge bg-success me-2">Hadir</span>
                        <span class="badge bg-warning text-dark me-2">Izin</span>
                    </div>
                    <div class="col-6">
                        <span class="badge bg-info me-2">Sakit</span>
                        <span class="badge bg-danger">Alpa</span>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="bi bi-lightbulb me-2"></i>
                    <strong>Tips:</strong> Gunakan filter tanggal untuk mendapatkan laporan periode tertentu, dan filter kelas untuk siswa dengan kelas spesifik.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Tutup</button>
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
.bg-gradient-danger {
    background: linear-gradient(45deg, #dc3545, #a02834);
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.card {
    transition: all 0.3s ease;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

@media print {
    .card-header, .btn, .modal {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle filter kelas berdasarkan jenis rekap
    const jenisRekap = document.getElementById('jenisRekap');
    const filterKelas = document.getElementById('filterKelas');
    
    if (jenisRekap) {
        jenisRekap.addEventListener('change', function() {
            filterKelas.style.display = (this.value === 'siswa') ? 'block' : 'none';
        });
    }

    // Export Excel
    const btnExcel = document.getElementById('exportExcel');
    if (btnExcel) {
        btnExcel.addEventListener('click', function() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);

            if (!formData.get('jenis_rekap')) {
                alert('Silakan pilih jenis rekap terlebih dahulu!');
                return;
            }

            const params = new URLSearchParams(formData);
            params.append('export', 'excel');

            const exportUrl = `{{ route('admin.rekap.export') }}?${params.toString()}`;
            window.location.href = exportUrl;
        });
    }

    // Export PDF
    const btnPDF = document.getElementById('exportPDF');
    if (btnPDF) {
        btnPDF.addEventListener('click', function() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);

            if (!formData.get('jenis_rekap')) {
                alert('Silakan pilih jenis rekap terlebih dahulu!');
                return;
            }

            const params = new URLSearchParams(formData);
            params.append('export', 'pdf');

            const exportUrl = `{{ route('admin.rekap.export') }}?${params.toString()}`;
            window.open(exportUrl, '_blank');
        });
    }

    // Set tanggal default (bulan ini)
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
    const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]');

    if (tanggalMulai && !tanggalMulai.value) {
        tanggalMulai.value = firstDay.toISOString().split('T')[0];
    }
    if (tanggalSelesai && !tanggalSelesai.value) {
        tanggalSelesai.value = lastDay.toISOString().split('T')[0];
    }

    // Validasi form submit
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            const jenisRekapVal = document.getElementById('jenisRekap').value;
            const tanggalMulaiVal = tanggalMulai.value;
            const tanggalSelesaiVal = tanggalSelesai.value;

            if (!jenisRekapVal) {
                e.preventDefault();
                alert('Silakan pilih jenis rekap terlebih dahulu!');
                return;
            }
            if (!tanggalMulaiVal || !tanggalSelesaiVal) {
                e.preventDefault();
                alert('Silakan isi tanggal mulai dan selesai!');
                return;
            }
            if (new Date(tanggalMulaiVal) > new Date(tanggalSelesaiVal)) {
                e.preventDefault();
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal selesai!');
                return;
            }
        });
    }
});

// ✅ TARUH DI LUAR supaya bisa diakses tombol onclick
function printReport() {
    window.print();
}
</script>
@endpush