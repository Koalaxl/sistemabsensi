@extends('layouts.guru.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-blue-700 flex items-center gap-2">
            <i class="bi bi-calendar-check text-blue-600"></i>
            📌 Agenda Kelas
        </h3>
        <a href="{{ route('guru.agenda.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2 shadow-md">
            <i class="bi bi-plus-circle"></i> Tambah Agenda
        </a>
    </div>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-800 border border-green-300">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-100 text-red-800 border border-red-300">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- Card Table -->
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden text-black">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border border-gray-200">
                <thead class="bg-blue-100 text-blue-800">
                    <tr>
                        <th class="px-4 py-2 border">No</th>
                        <th class="px-4 py-2 border">Judul</th>
                        <th class="px-4 py-2 border">Tanggal</th>
                        <th class="px-4 py-2 border">Mapel</th>
                        <th class="px-4 py-2 border">Kelas</th>
                        <th class="px-4 py-2 border">Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agenda as $i => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 border text-center font-medium">{{ $i+1 }}</td>
                        <td class="px-4 py-2 border">{{ $row->judul }}</td>
                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 border">{{ $row->mapel }}</td>
                        <td class="px-4 py-2 border">{{ $row->kelas }}</td>
                        <td class="px-4 py-2 border">{{ $row->guru->nama_guru ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                            🚫 Belum ada agenda
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>
@endsection
