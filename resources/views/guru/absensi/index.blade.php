@extends('layouts.guru.app')

@section('title', 'Absensi Saya')

@section('content')
<div class="container mx-auto p-2 sm:p-4 space-y-6 sm:space-y-8">
    {{-- Judul --}}
    <div class="text-center animate-fadeIn">
        <h1 class="text-lg sm:text-2xl md:text-3xl font-bold mb-1 sm:mb-2 text-blue-700">
            📘 Absensi Bulan {{ \Carbon\Carbon::parse($bulan)->translatedFormat('F Y') }}
        </h1>
        <p class="text-gray-700 text-xs sm:text-sm">Pantau kehadiran dan kedisiplinan siswa setiap hari</p>
    </div>

    {{-- Filter Bulan --}}
    <form method="GET" action="{{ route('guru.absensi.index') }}" 
          class="flex flex-col sm:flex-row items-center gap-2 justify-center animate-fadeIn delay-100">
        <input type="month" name="bulan" value="{{ $bulan }}"
            class="border rounded-lg px-2 sm:px-3 py-1.5 sm:py-2 w-full sm:w-auto
                   shadow-sm focus:ring-2 focus:ring-blue-300 text-xs sm:text-sm text-gray-800">
        <button type="submit"
            class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white 
                   px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg shadow 
                   hover:shadow-lg hover:scale-105 transform transition text-xs sm:text-sm w-full sm:w-auto">
            🔍 Tampilkan
        </button>
    </form>

    {{-- Kalender --}}
    <div class="bg-white shadow-lg rounded-2xl p-3 sm:p-5 animate-fadeIn delay-200">
        <div class="grid grid-cols-7 gap-1 sm:gap-2">
            {{-- Header Hari --}}
            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $day)
                <div class="font-semibold text-center py-1 sm:py-2 
                            text-[10px] sm:text-xs md:text-sm 
                            text-blue-600 border-b bg-blue-50 rounded">
                    {{ $day }}
                </div>
            @endforeach

            {{-- Spacer --}}
            @for($i=0; $i < $startOfMonth->dayOfWeekIso - 1; $i++)
                <div></div>
            @endfor

            {{-- Tanggal --}}
            @for($day=1; $day <= $daysInMonth; $day++)
                @php
                    $date = $startOfMonth->copy()->day($day);
                    $dateStr = $date->format('Y-m-d');
                    $status = $absensiMap[$dateStr] ?? 'Kosong';
                    $colorClass = $statusColors[$status];
                    $isToday = $date->isToday();
                @endphp
                <div class="aspect-square flex items-center justify-center 
                            rounded-md border font-bold 
                            text-[10px] sm:text-xs md:text-sm text-gray-800
                            {{ $colorClass }}
                            {{ $isToday ? 'ring-2 ring-indigo-400 ring-offset-1' : '' }}
                            hover:scale-105 transform transition duration-200 ease-in-out shadow-sm">
                    {{ $day }}
                </div>
            @endfor
        </div>
    </div>

    {{-- Rekapitulasi --}}
    <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg animate-fadeIn delay-300">
        <h2 class="text-sm sm:text-lg font-semibold mb-3 sm:mb-4 text-gray-800 flex items-center gap-2">
            📊 Rekapitulasi Bulan Ini
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-2 sm:gap-4 text-xs sm:text-sm text-gray-800">
            <div class="flex items-center gap-2 p-2 sm:p-3 rounded-lg bg-green-50 border border-green-200 shadow-sm">
                <span class="w-3 h-3 sm:w-4 sm:h-4 bg-green-500 rounded"></span> Hadir: <b>{{ $rekap['Hadir'] }}</b>
            </div>
            <div class="flex items-center gap-2 p-2 sm:p-3 rounded-lg bg-blue-50 border border-blue-200 shadow-sm">
                <span class="w-3 h-3 sm:w-4 sm:h-4 bg-blue-500 rounded"></span> Sakit: <b>{{ $rekap['Sakit'] }}</b>
            </div>
            <div class="flex items-center gap-2 p-2 sm:p-3 rounded-lg bg-yellow-50 border border-yellow-200 shadow-sm">
                <span class="w-3 h-3 sm:w-4 sm:h-4 bg-yellow-400 rounded"></span> Izin: <b>{{ $rekap['Izin'] }}</b>
            </div>
            <div class="flex items-center gap-2 p-2 sm:p-3 rounded-lg bg-red-50 border border-red-200 shadow-sm">
                <span class="w-3 h-3 sm:w-4 sm:h-4 bg-red-500 rounded"></span> Alpha: <b>{{ $rekap['Alpha'] }}</b>
            </div>
            <div class="flex items-center gap-2 p-2 sm:p-3 rounded-lg bg-gray-50 border border-gray-200 shadow-sm">
                <span class="w-3 h-3 sm:w-4 sm:h-4 bg-gray-300 rounded border"></span> Kosong: <b>{{ $rekap['Kosong'] }}</b>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; }
.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
</style>
@endpush
