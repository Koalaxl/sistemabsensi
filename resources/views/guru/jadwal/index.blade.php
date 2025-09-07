@extends('layouts.guru.app')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-6 font-bold text-2xl text-gray-800">📅 Jadwal Saya</h2>

    {{-- Jadwal Piket --}}
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-blue-700 mb-4">🛡️ Jadwal Piket</h3>
        @if($jadwalPiket->isEmpty())
            <p class="text-gray-500 italic">Tidak ada jadwal piket.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($jadwalPiket as $piket)
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition transform hover:-translate-y-1">
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-500 text-white w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold">
                                {{ strtoupper(substr($piket->hari, 0, 2)) }}
                            </div>
                            <h4 class="ml-4 text-lg font-semibold text-gray-800">{{ $piket->hari }}</h4>
                        </div>
                        <p class="text-gray-600"><i class="fas fa-clock mr-2"></i>{{ $piket->jam_mulai }} - {{ $piket->jam_selesai }}</p>
                        @if($piket->keterangan)
                            <p class="text-gray-500 mt-2"><i class="fas fa-info-circle mr-2"></i>{{ $piket->keterangan }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Jadwal Mengajar --}}
    <div>
        <h3 class="text-xl font-semibold text-green-700 mb-4">📖 Jadwal Mengajar</h3>
        @if($jadwalMengajar->isEmpty())
            <p class="text-gray-500 italic">Tidak ada jadwal mengajar.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($jadwalMengajar as $jadwal)
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-2xl transition transform hover:-translate-y-1">
                        <div class="flex items-center mb-3">
                            <div class="bg-green-500 text-white w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold">
                                {{ strtoupper(substr($jadwal->hari, 0, 2)) }}
                            </div>
                            <h4 class="ml-4 text-lg font-semibold text-gray-800">{{ $jadwal->hari }}</h4>
                        </div>
                        <p class="text-gray-600"><i class="fas fa-clock mr-2"></i>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</p>
                        <p class="text-gray-700 mt-2"><i class="fas fa-book mr-2"></i>{{ $jadwal->mata_pelajaran }}</p>
                        <p class="text-gray-700"><i class="fas fa-users mr-2"></i>Kelas {{ $jadwal->kelas }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
