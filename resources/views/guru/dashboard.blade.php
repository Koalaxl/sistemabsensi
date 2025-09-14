@extends('layouts.guru.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Sapaan --}}
    <div class="mb-10 animate-fadeIn">
        <p class="text-gray-600">
            Senang bertemu kembali! Pilih menu di bawah untuk mulai aktivitas Anda.
        </p>
    </div>

    {{-- Grid Menu --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 gap-y-8">
        {{-- Absensi Guru --}}
        <a href="{{ route('guru.absensi.index') }}" 
           class="group bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transform transition duration-300 ease-out animate-fadeIn">
            <div class="flex items-center gap-4">
                <div class="bg-white/30 p-5 rounded-full group-hover:scale-110 transform transition shadow">
                    <i class="fas fa-user-check text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Absensi Saya</h3>
                    <p class="text-sm opacity-80">Lihat & catat kehadiran anda</p>
                </div>
            </div>
        </a>

        {{-- Kehadiran Siswa --}}
        <a href="{{ route('guru.absensi_siswa.index') }}" 
           class="group bg-gradient-to-r from-green-500 to-green-700 text-white p-6 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transform transition duration-300 ease-out animate-fadeIn delay-100">
            <div class="flex items-center gap-4">
                <div class="bg-white/30 p-5 rounded-full group-hover:scale-110 transform transition shadow">
                    <i class="fas fa-users text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Kehadiran Siswa</h3>
                    <p class="text-sm opacity-80">Pantau absensi siswa anda</p>
                </div>
            </div>
        </a>

        {{-- Jadwal --}}
        <a href="{{ route('guru.jadwal.index') }}" 
           class="group bg-gradient-to-r from-purple-500 to-purple-700 text-white p-6 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transform transition duration-300 ease-out animate-fadeIn delay-200">
            <div class="flex items-center gap-4">
                <div class="bg-white/30 p-5 rounded-full group-hover:scale-110 transform transition shadow">
                    <i class="fas fa-calendar-alt text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Jadwal</h3>
                    <p class="text-sm opacity-80">Lihat jadwal mengajar</p>
                </div>
            </div>
        </a>

        {{-- Rekap Absensi --}}
        <a href="{{ route('guru.rekap_siswa.index') }}" 
           class="group bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transform transition duration-300 ease-out animate-fadeIn delay-300">
            <div class="flex items-center gap-4">
                <div class="bg-white/30 p-5 rounded-full group-hover:scale-110 transform transition shadow">
                    <i class="fas fa-clipboard-list text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Rekap Absensi</h3>
                    <p class="text-sm opacity-80">Ringkasan absensi siswa</p>
                </div>
            </div>
        </a>

        {{-- Agenda Kelas --}}
        <a href="{{ route('guru.agenda.index') }}" 
           class="group bg-gradient-to-r from-red-500 to-red-700 text-white p-6 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transform transition duration-300 ease-out animate-fadeIn delay-400">
            <div class="flex items-center gap-4">
                <div class="bg-white/30 p-5 rounded-full group-hover:scale-110 transform transition shadow">
                    <i class="fas fa-book text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Agenda Kelas</h3>
                    <p class="text-sm opacity-80">Kelola agenda pembelajaran</p>
                </div>
            </div>
        </a>

        {{-- Logout --}}
        <a href="{{ route('logout') }}" 
           class="group bg-gradient-to-r from-gray-700 to-gray-900 text-white p-6 rounded-3xl shadow-lg hover:shadow-2xl hover:-translate-y-2 transform transition duration-300 ease-out animate-fadeIn delay-500">
            <div class="flex items-center gap-4">
                <div class="bg-white/30 p-5 rounded-full group-hover:scale-110 transform transition shadow">
                    <i class="fas fa-sign-out-alt text-3xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold">Logout</h3>
                    <p class="text-sm opacity-80">Keluar dari sistem</p>
                </div>
            </div>
        </a>
    </div>
@endsection

{{-- Animasi FadeIn --}}
@push('styles')
<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
  animation: fadeIn 0.6s ease-out forwards;
}
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }
.delay-500 { animation-delay: 0.5s; }
</style>
@endpush
