@extends('layouts.guru.app')

@section('content')
<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-6 sm:py-8">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="font-bold text-xl sm:text-2xl text-gray-800 flex items-center gap-2">
            📅 Jadwal Saya
        </h2>
        <a href="{{ route('guru.jadwal.create') }}" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm sm:text-base transition shadow-md">
            Buat Jadwal
        </a>
    </div>

    <!-- Jadwal Piket -->
    <div class="mb-10">
        <h3 class="text-lg sm:text-xl font-semibold text-blue-700 mb-4">🛡️ Jadwal Piket</h3>

        @if($jadwalPiket->isEmpty())
            <p class="text-gray-500 italic text-sm sm:text-base">Tidak ada jadwal piket.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($jadwalPiket as $piket)
                    <div class="bg-white rounded-2xl shadow-md sm:shadow-lg p-4 sm:p-6 
                                hover:shadow-2xl transition transform hover:-translate-y-1 duration-300">
                        
                        <!-- Hari -->
                        <div class="flex items-center mb-3">
                            <div class="bg-blue-500 text-white w-10 h-10 sm:w-12 sm:h-12 
                                        rounded-full flex items-center justify-center 
                                        text-sm sm:text-lg font-bold">
                                {{ strtoupper(substr($piket->hari, 0, 2)) }}
                            </div>
                            <h4 class="ml-3 sm:ml-4 text-base sm:text-lg font-semibold text-gray-800">
                                {{ $piket->hari }}
                            </h4>
                        </div>

                        <!-- Jam -->
                        <p class="text-gray-600 text-sm sm:text-base">
                            ⏰ {{ $piket->jam_mulai }} - {{ $piket->jam_selesai }}
                        </p>

                        <!-- Keterangan -->
                        @if($piket->keterangan)
                            <p class="text-gray-500 mt-2 text-sm sm:text-base">
                                ℹ️ {{ $piket->keterangan }}
                            </p>
                        @endif

                        <!-- Tombol Aksi -->
                        <!-- <div class="flex space-x-2 mt-4">
                            <a href="{{ route('guru.jadwal_piket.edit', $piket->id) }}" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm shadow">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('guru.jadwal_piket.destroy', $piket->id) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm shadow">
                                    🗑️ Hapus
                                </button>
                            </form> -->
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Jadwal Mengajar -->
    <div>
        <h3 class="text-lg sm:text-xl font-semibold text-green-700 mb-4">📖 Jadwal Mengajar</h3>

        @if($jadwalMengajar->isEmpty())
            <p class="text-gray-500 italic text-sm sm:text-base">Tidak ada jadwal mengajar.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($jadwalMengajar as $jadwal)
                    <div class="bg-white rounded-2xl shadow-md sm:shadow-lg p-4 sm:p-6 
                                hover:shadow-2xl transition transform hover:-translate-y-1 duration-300">

                        <!-- Hari -->
                        <div class="flex items-center mb-3">
                            <div class="bg-green-500 text-white w-10 h-10 sm:w-12 sm:h-12 
                                        rounded-full flex items-center justify-center 
                                        text-sm sm:text-lg font-bold">
                                {{ strtoupper(substr($jadwal->hari, 0, 2)) }}
                            </div>
                            <h4 class="ml-3 sm:ml-4 text-base sm:text-lg font-semibold text-gray-800">
                                {{ $jadwal->hari }}
                            </h4>
                        </div>

                        <!-- Jam -->
                        <p class="text-gray-600 text-sm sm:text-base">
                            ⏰ {{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}
                        </p>

                        <!-- Mapel -->
                        <p class="text-gray-700 mt-2 text-sm sm:text-base">
                            📘 {{ $jadwal->mata_pelajaran }}
                        </p>

                        <!-- Kelas -->
                        <p class="text-gray-700 text-sm sm:text-base">
                            👥 Kelas {{ $jadwal->kelas }}
                        </p>

                        <!-- Tombol Aksi -->
                        <!-- <div class="flex space-x-2 mt-4">
                            <a href="{{ route('guru.jadwal_guru.edit', $jadwal->id) }}" 
                               class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-sm shadow">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('guru.jadwal_guru.destroy', $jadwal->id) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-sm shadow">
                                    🗑️ Hapus
                                </button>
                            </form>
                        </div> -->
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
