@extends('layouts.guru.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

    <!-- Header -->
    <h2 class="font-bold text-2xl text-black mb-6 flex items-center gap-2">
        ➕ Buat Jadwal
    </h2>

    <!-- Pilih Jenis Jadwal -->
    <div class="mb-6">
        <label class="block text-sm font-semibold text-black mb-2">Jenis Jadwal</label>
        <select id="jenisJadwal" 
                class="w-full border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 text-black px-4 py-2">
            <option value="">-- Pilih Jenis Jadwal --</option>
            <option value="piket">🛡️ Jadwal Piket</option>
            <option value="mengajar">📖 Jadwal Mengajar</option>
        </select>
    </div>

    <!-- FORM JADWAL PIKET -->
    <form id="formPiket" action="{{ route('guru.jadwal.storePiket') }}" method="POST"
          class="hidden bg-gradient-to-br from-blue-50 to-white border border-blue-200 p-6 sm:p-8 rounded-2xl shadow-lg space-y-6 transition duration-300">
        @csrf

        <h3 class="text-lg font-semibold text-blue-700 flex items-center gap-2">
            🛡️ Form Jadwal Piket
        </h3>

        <!-- ID Guru -->
        <div>
            <label class="block text-sm font-semibold text-black">ID Guru</label>
            <input type="number" name="id_guru" placeholder="Masukkan ID Guru"
                   class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-blue-400">
        </div>

        <!-- Hari -->
        <div>
            <label class="block text-sm font-semibold text-black">Hari</label>
            <select name="hari" class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-blue-400">
                <option value="">-- Pilih Hari --</option>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
            </select>
        </div>

        <!-- Jam -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-black">Jam Mulai</label>
                <input type="time" name="jam_mulai" 
                       class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-blue-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-black">Jam Selesai</label>
                <input type="time" name="jam_selesai" 
                       class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-blue-400">
            </div>
        </div>

        <!-- Keterangan -->
        <div>
            <label class="block text-sm font-semibold text-black">Keterangan</label>
            <textarea name="keterangan" rows="3" 
                      class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-blue-400"></textarea>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('guru.jadwal.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-black px-4 py-2 rounded-xl text-sm shadow transition">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl text-sm shadow transition">
                💾 Simpan Piket
            </button>
        </div>
    </form>

    <!-- FORM JADWAL MENGAJAR -->
    <form id="formMengajar" action="{{ route('guru.jadwal.storeMengajar') }}" method="POST"
          class="hidden bg-gradient-to-br from-green-50 to-white border border-green-200 p-6 sm:p-8 rounded-2xl shadow-lg space-y-6 transition duration-300">
        @csrf

        <h3 class="text-lg font-semibold text-green-700 flex items-center gap-2">
            📖 Form Jadwal Mengajar
        </h3>

        <!-- ID Guru -->
        <div>
            <label class="block text-sm font-semibold text-black">ID Guru</label>
            <input type="number" name="id_guru" placeholder="Masukkan ID Guru"
                   class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-green-400">
        </div>

        <!-- Hari -->
        <div>
            <label class="block text-sm font-semibold text-black">Hari</label>
            <select name="hari" class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-green-400">
                <option value="">-- Pilih Hari --</option>
                <option value="Senin">Senin</option>
                <option value="Selasa">Selasa</option>
                <option value="Rabu">Rabu</option>
                <option value="Kamis">Kamis</option>
                <option value="Jumat">Jumat</option>
                <option value="Sabtu">Sabtu</option>
            </select>
        </div>

        <!-- Jam -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-black">Jam Mulai</label>
                <input type="time" name="jam_mulai" 
                       class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-green-400">
            </div>
            <div>
                <label class="block text-sm font-semibold text-black">Jam Selesai</label>
                <input type="time" name="jam_selesai" 
                       class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-green-400">
            </div>
        </div>

        <!-- Mata Pelajaran -->
        <div>
            <label class="block text-sm font-semibold text-black">Mata Pelajaran</label>
            <input type="text" name="mata_pelajaran" placeholder="Contoh: Matematika"
                   class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-green-400">
        </div>

        <!-- Kelas -->
        <div>
            <label class="block text-sm font-semibold text-black">Kelas</label>
            <select name="kelas" class="w-full border-gray-300 rounded-xl shadow-sm text-black px-4 py-2 focus:ring-2 focus:ring-green-400">
                <option value="">-- Pilih Kelas --</option>
                @foreach($kelas as $k)
                    <option value="{{ $k }}">{{ $k }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tombol -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('guru.jadwal.index') }}" 
               class="bg-gray-200 hover:bg-gray-300 text-black px-4 py-2 rounded-xl text-sm shadow transition">
                Batal
            </a>
            <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-xl text-sm shadow transition">
                💾 Simpan Jadwal
            </button>
        </div>
    </form>
</div>

<script>
    const jenisSelect = document.getElementById('jenisJadwal');
    const formPiket = document.getElementById('formPiket');
    const formMengajar = document.getElementById('formMengajar');

    jenisSelect.addEventListener('change', function () {
        formPiket.classList.add('hidden');
        formMengajar.classList.add('hidden');

        if (this.value === 'piket') {
            formPiket.classList.remove('hidden');
        } else if (this.value === 'mengajar') {
            formMengajar.classList.remove('hidden');
        }
    });
</script>
@endsection
