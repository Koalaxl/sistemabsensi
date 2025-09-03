@extends('layouts.admin.app')

@section('content')
<h2 class="mb-4">📊 Dashboard Kehadiran</h2>

<style>
/* Animasi fade-in + slide-up */
.fade-slide-up {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeSlideUp 0.8s forwards;
}

@keyframes fadeSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- Form Filter -->
<form method="GET" action="{{ route('admin.dashboard') }}" class="mb-5 fade-slide-up" style="animation-delay: 0.1s;">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="tanggal" class="form-label fw-bold">Filter Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control"
                max="{{ now()->format('Y-m-d') }}"
                min="{{ now()->subDays(4)->format('Y-m-d') }}"
                value="{{ $tanggal }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill"></i> Filter</button>
        </div>
    </div>
</form>

<!-- Total Data -->
<div class="row g-4 mb-5 fade-slide-up" style="animation-delay: 0.2s;">
    <div class="col-md-4">
        <div class="card rounded-4 text-center p-4 bg-primary text-white shadow-lg" style="transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <h5>Jumlah Siswa</h5>
            <p class="display-4 mb-0">{{ $totalSiswa ?? 0 }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card rounded-4 text-center p-4 bg-info text-white shadow-lg" style="transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <h5>Jumlah Guru</h5>
            <p class="display-4 mb-0">{{ $totalGuru ?? 0 }}</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card rounded-4 text-center p-4 bg-warning text-dark shadow-lg" style="transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <h5>Jumlah Wali Kelas</h5>
            <p class="display-4 mb-0">{{ $totalWaliKelas ?? 0 }}</p>
        </div>
    </div>
</div>

<!-- Kehadiran Charts -->
<div class="row mb-5 g-4 fade-slide-up" style="animation-delay: 0.3s;">
    <div class="col-md-6">
        <div class="card rounded-4 border-0 shadow-lg">
            <div class="card-header bg-primary text-white rounded-top-4">
                <h5 class="mb-0 fw-bold">Kehadiran Siswa</h5>
            </div>
            <div class="card-body">
                <canvas id="chartKehadiranSiswa" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card rounded-4 border-0 shadow-lg">
            <div class="card-header bg-info text-white rounded-top-4">
                <h5 class="mb-0 fw-bold">Kehadiran Guru</h5>
            </div>
            <div class="card-body">
                <canvas id="chartKehadiranGuru" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan Kehadiran -->
<div class="row g-4 mb-5 fade-slide-up" style="animation-delay: 0.4s;">
    @php
        $colors = [
            'Hadir' => ['bg' => 'success', 'text' => 'text-white'],
            'Izin' => ['bg' => 'warning', 'text' => 'text-dark'],
            'Sakit' => ['bg' => 'danger', 'text' => 'text-white'],
            'Alpha' => ['bg' => 'secondary', 'text' => 'text-white']
        ];
    @endphp

    <div class="col-12"><h4 class="mb-3 fw-bold">Ringkasan Kehadiran Siswa</h4></div>
    @foreach(['Hadir' => $hadirSiswa, 'Izin' => $izinSiswa, 'Sakit' => $sakitSiswa, 'Alpha' => $alphaSiswa] as $key => $value)
    <div class="col-md-3">
        <div class="card rounded-4 shadow-lg text-center border-0 bg-{{ $colors[$key]['bg'] }} {{ $colors[$key]['text'] }} p-4 fade-slide-up" style="animation-delay: 0.{{ $loop->iteration + 4 }}s;">
            <h6 class="fw-bold">{{ $key }}</h6>
            <p class="display-5 mb-0">{{ $value ?? 0 }}</p>
        </div>
    </div>
    @endforeach

    <div class="col-12 mt-4"><h4 class="mb-3 fw-bold">Ringkasan Kehadiran Guru</h4></div>
    @foreach(['Hadir' => $hadirGuru, 'Izin' => $izinGuru, 'Sakit' => $sakitGuru, 'Alpha' => $alphaGuru] as $key => $value)
    <div class="col-md-3">
        <div class="card rounded-4 shadow-lg text-center border-0 bg-{{ $colors[$key]['bg'] }} {{ $colors[$key]['text'] }} p-4 fade-slide-up" style="animation-delay: 0.{{ $loop->iteration + 8 }}s;">
            <h6 class="fw-bold">{{ $key }}</h6>
            <p class="display-5 mb-0">{{ $value ?? 0 }}</p>
        </div>
    </div>
    @endforeach
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxSiswa = document.getElementById('chartKehadiranSiswa').getContext('2d');
const ctxGuru = document.getElementById('chartKehadiranGuru').getContext('2d');

const dataSiswa = {!! json_encode([$hadirSiswa, $izinSiswa, $sakitSiswa, $alphaSiswa]) !!};
const dataGuru = {!! json_encode([$hadirGuru, $izinGuru, $sakitGuru, $alphaGuru]) !!};
const totalSiswa = {{ $totalSiswa ?? 0 }};
const totalGuru = {{ $totalGuru ?? 0 }};

new Chart(ctxSiswa, {
    type: 'bar',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            label: 'Kehadiran Siswa',
            data: dataSiswa,
            backgroundColor: ['#198754','#ffc107','#dc3545','#6c757d'],
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        animation: {
            duration: 1200,
            easing: 'easeOutCubic',
            onComplete: function() {
                const chartContainer = document.getElementById('chartKehadiranSiswa').parentNode;
                chartContainer.style.opacity = 1;
            }
        },
        scales: { y: { beginAtZero: true, max: totalSiswa, ticks: { stepSize: 1 } } },
        plugins: { legend: { display: false } }
    }
});

new Chart(ctxGuru, {
    type: 'bar',
    data: {
        labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
        datasets: [{
            label: 'Kehadiran Guru',
            data: dataGuru,
            backgroundColor: ['#198754','#ffc107','#dc3545','#6c757d'],
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        animation: {
            duration: 1200,
            easing: 'easeOutCubic',
            onComplete: function() {
                const chartContainer = document.getElementById('chartKehadiranGuru').parentNode;
                chartContainer.style.opacity = 1;
            }
        },
        scales: { y: { beginAtZero: true, max: totalGuru, ticks: { stepSize: 1 } } },
        plugins: { legend: { display: false } }
    }
});
</script>
@endsection
