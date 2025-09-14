@extends('layouts.guru.app')

@section('title', 'Absensi Siswa')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Judul --}}
    <div class="text-center mb-8">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-blue-700 mb-2">
            <i class="fas fa-users mr-2"></i> Absensi Siswa
        </h1>
        <p class="text-sm sm:text-base text-gray-600">📚 Kelola kehadiran siswa dengan tampilan modern dan interaktif</p>
    </div>

    {{-- Pilih Kelas --}}
    <div class="bg-white shadow-md rounded-xl p-4 sm:p-6 mb-6">
        <form action="{{ route('guru.absensi_siswa.index') }}" method="GET" 
              class="grid grid-cols-1 sm:grid-cols-5 gap-4">
            <div class="sm:col-span-3">
                <label for="kelas" class="block text-sm font-semibold text-gray-700 mb-1">Pilih Kelas</label>
                <select name="kelas" id="kelas" 
                    class="w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-gray-800 text-sm sm:text-base"
                    required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($listKelas as $k)
                        <option value="{{ $k }}" {{ ($kelasDipilih ?? '') == $k ? 'selected' : '' }}>
                            {{ $k }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2 flex sm:block">
                <button type="submit" 
                    class="flex-1 sm:w-full py-2 px-3 rounded-lg bg-gradient-to-r from-blue-600 to-green-500 text-white font-semibold shadow hover:scale-105 transition text-sm sm:text-base">
                    <i class="fas fa-search mr-1"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    {{-- Form Absensi --}}
    @if(!empty($siswa))
    <div class="bg-white shadow-md rounded-xl p-4 sm:p-6">
        <form action="{{ route('guru.absensi_siswa.store') }}" method="POST">
            @csrf
            <input type="hidden" name="kelas" value="{{ $kelasDipilih }}">

            {{-- Tanggal --}}
            <div class="mb-4 sm:mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" 
                       class="w-full sm:w-auto rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-gray-800 text-sm sm:text-base"
                       required>
            </div>

            {{-- Tabel absensi --}}
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full text-gray-800 text-sm sm:text-base">
                    <thead class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-xs sm:text-sm md:text-base">
                        <tr>
                            <th class="px-2 sm:px-4 py-2 w-10 sm:w-12">No</th>
                            <th class="px-2 sm:px-4 py-2 text-left">Nama Siswa</th>
                            <th class="px-2 sm:px-4 py-2">Hadir</th>
                            <th class="px-2 sm:px-4 py-2">Izin</th>
                            <th class="px-2 sm:px-4 py-2">Sakit</th>
                            <th class="px-2 sm:px-4 py-2 w-40 sm:w-56">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($siswa as $index => $s)
                        <tr class="hover:bg-blue-50 transition">
                            <td class="px-2 sm:px-4 py-2 text-center">{{ $index + 1 }}</td>
                            <td class="px-2 sm:px-4 py-2 text-left font-medium">{{ $s->nama_siswa }}</td>
                            <td class="px-2 sm:px-4 py-2 text-center">
                                <input type="radio" name="kehadiran[{{ $index }}][status]" value="Hadir"
                                    class="text-green-600 focus:ring-green-500">
                            </td>
                            <td class="px-2 sm:px-4 py-2 text-center">
                                <input type="radio" name="kehadiran[{{ $index }}][status]" value="Izin"
                                    class="text-blue-500 focus:ring-blue-400">
                            </td>
                            <td class="px-2 sm:px-4 py-2 text-center">
                                <input type="radio" name="kehadiran[{{ $index }}][status]" value="Sakit"
                                    class="text-yellow-500 focus:ring-yellow-400">
                            </td>
                            <td class="px-2 sm:px-4 py-2">
                                <input type="text" name="kehadiran[{{ $index }}][keterangan]" 
                                    placeholder="Tambahkan keterangan..."
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-gray-800 text-sm sm:text-base">
                            </td>
                            <input type="hidden" name="kehadiran[{{ $index }}][siswa_id]" value="{{ $s->id }}">
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tombol Simpan --}}
            <div class="mt-6 text-right">
                <button type="submit" 
                    class="py-2 sm:py-3 px-4 sm:px-6 rounded-lg bg-gradient-to-r from-green-500 to-cyan-500 text-white font-bold shadow-lg hover:scale-105 transition text-sm sm:text-base">
                    <i class="fas fa-save mr-2"></i> Simpan Kehadiran
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
