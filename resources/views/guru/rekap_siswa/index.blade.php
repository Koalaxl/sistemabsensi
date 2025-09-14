@extends('layouts.guru.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Header -->
    <div class="text-center mb-8">
        <h3 class="text-3xl font-bold text-blue-700 flex items-center justify-center gap-2">
            <i class="bi bi-people-fill text-blue-600"></i>
            Rekap Kehadiran Siswa
        </h3>
        <p class="text-gray-500">Kelola, filter, dan unduh laporan kehadiran siswa</p>
    </div>

    <!-- Filter -->
    <div class="bg-white shadow-lg rounded-2xl p-6 mb-8">
        <form method="GET" action="{{ route('guru.rekap_siswa.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-semibold text-black">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}"
                    class="mt-1 w-full rounded-lg border-gray-400 focus:ring-2 focus:ring-black focus:border-black text-black" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-black">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}"
                    class="mt-1 w-full rounded-lg border-gray-400 focus:ring-2 focus:ring-black focus:border-black text-black" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-black">Kelas</label>
                <select name="kelas"
                    class="mt-1 w-full rounded-lg border-gray-400 focus:ring-2 focus:ring-black focus:border-black text-black">
                    <option value="">Semua</option>
                    @foreach($listKelas as $k)
                        <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-black">Status</label>
                <select name="status"
                    class="mt-1 w-full rounded-lg border-gray-400 focus:ring-2 focus:ring-black focus:border-black text-black">
                    <option value="">Semua</option>
                    <option value="Hadir" {{ $status == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Izin" {{ $status == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option value="Sakit" {{ $status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="Alpa" {{ $status == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-black hover:bg-gray-800 text-white font-medium py-2 px-4 rounded-lg flex items-center justify-center gap-2">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Statistik -->
    @if($statistik)
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-center shadow-sm">
            <h4 class="text-2xl font-bold text-green-600">{{ $statistik['hadir'] }}</h4>
            <p class="text-gray-600 flex items-center justify-center gap-1"><i class="bi bi-check-circle text-green-500"></i> Hadir</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center shadow-sm">
            <h4 class="text-2xl font-bold text-yellow-600">{{ $statistik['izin'] }}</h4>
            <p class="text-gray-600 flex items-center justify-center gap-1"><i class="bi bi-envelope-paper text-yellow-500"></i> Izin</p>
        </div>
        <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 text-center shadow-sm">
            <h4 class="text-2xl font-bold text-sky-600">{{ $statistik['sakit'] }}</h4>
            <p class="text-gray-600 flex items-center justify-center gap-1"><i class="bi bi-emoji-frown text-sky-500"></i> Sakit</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-center shadow-sm">
            <h4 class="text-2xl font-bold text-red-600">{{ $statistik['alpa'] }}</h4>
            <p class="text-gray-600 flex items-center justify-center gap-1"><i class="bi bi-x-circle text-red-500"></i> Alpa</p>
        </div>
    </div>
    @endif

    <!-- Tabel + Card Responsif -->
    <div class="bg-white shadow-md rounded-2xl p-6">
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-lg font-bold text-blue-700">📊 Hasil Rekap</h5>
            @if($dataRekap->count() > 0)
            <div class="flex gap-2">
                <a href="{{ route('guru.rekap_siswa.export', array_merge(request()->all(), ['export' => 'excel'])) }}"
                    class="bg-green-600 hover:bg-green-700 text-white text-sm px-4 py-2 rounded-full flex items-center gap-1">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
                <a href="{{ route('guru.rekap_siswa.export', array_merge(request()->all(), ['export' => 'pdf'])) }}"
                    class="bg-red-600 hover:bg-red-700 text-white text-sm px-4 py-2 rounded-full flex items-center gap-1">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
            </div>
            @endif
        </div>

        @if($dataRekap->count() > 0)
        <div class="overflow-x-auto relative">
            <!-- Tabel untuk desktop -->
            <table class="hidden md:table min-w-[800px] text-sm border border-gray-200 rounded-lg overflow-hidden w-full">
                <thead class="bg-blue-100 text-blue-800 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-2 border text-black">No</th>
                        <th class="px-4 py-2 border text-black">Nama Siswa</th>
                        <th class="px-4 py-2 border text-black">NISN</th>
                        <th class="px-4 py-2 border text-black">Kelas</th>
                        <th class="px-4 py-2 border text-black">Tanggal</th>
                        <th class="px-4 py-2 border text-black">Status</th>
                        <th class="px-4 py-2 border text-black">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataRekap as $i => $row)
                    <tr class="hover:bg-gray-50 text-sm">
                        <td class="px-4 py-2 border font-medium text-black">{{ $i+1 }}</td>
                        <td class="px-4 py-2 border text-black">{{ $row->siswa->nama_siswa ?? '-' }}</td>
                        <td class="px-4 py-2 border text-black">{{ $row->siswa->nisn ?? '-' }}</td>
                        <td class="px-4 py-2 border text-black">{{ $row->siswa->kelas ?? '-' }}</td>
                        <td class="px-4 py-2 border text-black">
                            {{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-2 border">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold text-black
                                @if($row->status == 'hadir') bg-green-100
                                @elseif($row->status == 'sakit') bg-yellow-100
                                @elseif($row->status == 'izin') bg-gray-200
                                @else bg-red-100 @endif">
                                {{ ucfirst($row->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2 border text-black">{{ $row->keterangan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Card untuk mobile -->
            <div class="block md:hidden space-y-4">
                @foreach($dataRekap as $i => $row)
                <div class="border rounded-lg p-4 shadow-sm bg-white">
                    <p class="text-sm font-semibold text-black">{{ $i+1 }}. {{ $row->siswa->nama_siswa ?? '-' }}</p>
                    <p class="text-xs text-gray-600">NISN: <span class="text-black">{{ $row->siswa->nisn ?? '-' }}</span></p>
                    <p class="text-xs text-gray-600">Kelas: <span class="text-black">{{ $row->siswa->kelas ?? '-' }}</span></p>
                    <p class="text-xs text-gray-600">Tanggal: <span class="text-black">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</span></p>
                    <p class="text-xs text-gray-600">Status:
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold text-black
                            @if($row->status == 'hadir') bg-green-100
                            @elseif($row->status == 'sakit') bg-yellow-100
                            @elseif($row->status == 'izin') bg-gray-200
                            @else bg-red-100 @endif">
                            {{ ucfirst($row->status) }}
                        </span>
                    </p>
                    <p class="text-xs text-gray-600">Keterangan: <span class="text-black">{{ $row->keterangan ?? '-' }}</span></p>
                </div>
                @endforeach
            </div>

            <!-- Pagination sticky -->
            <div class="sticky bottom-0 bg-white py-3 border-t flex justify-center">
                {{ $dataRekap->links() }}
            </div>
        </div>
        @else
            <p class="text-gray-500">🚫 Tidak ada data untuk ditampilkan.</p>
        @endif
    </div>
</div>

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>
@endsection
