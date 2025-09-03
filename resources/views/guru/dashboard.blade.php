@extends('layouts.guru.app')

@section('title', 'Dashboard')

@section('content')
    <h2 class="text-2xl font-bold mb-6">Selamat Datang, {{ Auth::user()->nama_pengguna ?? 'Guru' }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Absensi Guru --}}
        <a href="{{ route('guru.absensi.index') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-xl shadow flex flex-col items-center transition">
            <i class="fas fa-user-check text-4xl mb-3"></i>
            <span class="text-lg font-semibold">Absensi Saya</span>
        </a>

        {{-- Kehadiran Siswa --}}
        <a href="{{ route('guru.absensi_siswa.index') }}" 
           class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-xl shadow flex flex-col items-center transition">
            <i class="fas fa-users text-4xl mb-3"></i>
            <span class="text-lg font-semibold">Kehadiran Siswa</span>
        </a>

        {{-- Jadwal --}}
        <a href="{{ route('guru.jadwal.index') }}" 
           class="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-xl shadow flex flex-col items-center transition">
            <i class="fas fa-calendar text-4xl mb-3"></i>
            <span class="text-lg font-semibold">Jadwal</span>
        </a>

        {{-- Rekap Absensi --}}
        <a href="{{ route('guru.rekap.absen') }}" 
           class="bg-yellow-500 hover:bg-yellow-600 text-white p-6 rounded-xl shadow flex flex-col items-center transition">
            <i class="fas fa-clipboard-list text-4xl mb-3"></i>
            <span class="text-lg font-semibold">Rekap Absensi</span>
        </a>

        {{-- Agenda Kelas --}}
        <a href="{{ route('guru.agenda.kelas') }}" 
           class="bg-red-500 hover:bg-red-600 text-white p-6 rounded-xl shadow flex flex-col items-center transition">
            <i class="fas fa-book text-4xl mb-3"></i>
            <span class="text-lg font-semibold">Agenda Kelas</span>
        </a>

        {{-- Logout --}}
        <a href="{{ route('logout') }}" 
           class="bg-gray-700 hover:bg-gray-800 text-white p-6 rounded-xl shadow flex flex-col items-center transition">
            <i class="fas fa-sign-out-alt text-4xl mb-3"></i>
            <span class="text-lg font-semibold">Logout</span>
        </a>
    </div>
@endsection
