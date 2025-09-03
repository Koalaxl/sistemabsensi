{{-- resources/views/guru/absensi/index.blade.php --}}
@extends('layouts.guru.app')

@section('title', 'Absensi Saya')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Absensi Bulan {{ \Carbon\Carbon::now()->format('F Y') }}</h1>

    @php
        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
        $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
        $daysInMonth = $startOfMonth->daysInMonth;

        // Buat array key = tanggal, value = status
        $absensiMap = [];
        foreach($kehadiran as $k) {
            $absensiMap[\Carbon\Carbon::parse($k->tanggal)->format('Y-m-d')] = $k->status;
        }

        // Warna status
        $statusColors = [
            'Hadir' => 'bg-green-500 text-white',
            'Sakit' => 'bg-blue-500 text-white',
            'Izin'  => 'bg-yellow-400 text-white',
            'Alpha' => 'bg-red-500 text-white',
            'Kosong' => 'bg-gray-300 text-gray-700'
        ];
    @endphp

    <div class="grid grid-cols-7 gap-2">
        {{-- Header Hari --}}
        @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $day)
            <div class="font-bold text-center py-2">{{ $day }}</div>
        @endforeach

        {{-- Spacer untuk hari pertama --}}
        @for($i=0; $i < $startOfMonth->dayOfWeekIso - 1; $i++)
            <div></div>
        @endfor

        {{-- Tanggal --}}
        @for($day=1; $day <= $daysInMonth; $day++)
            @php
                $date = $startOfMonth->copy()->day($day)->format('Y-m-d');
                $status = $absensiMap[$date] ?? 'Kosong';
                $colorClass = $statusColors[$status];
            @endphp
            <div class="h-16 flex items-center justify-center rounded {{ $colorClass }}">
                <div class="text-center">
                    <div class="font-bold">{{ $day }}</div>
                    <div class="text-sm">{{ $status }}</div>
                </div>
            </div>
        @endfor
    </div>
</div>
@endsection
