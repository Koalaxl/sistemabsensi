@extends('layouts.guru.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8 text-gray-900">
    <!-- Header -->
    <h3 class="text-2xl font-bold mb-6">📝 Tambah Agenda</h3>

    <!-- Card Form -->
    <div class="bg-white shadow-lg rounded-2xl p-6">
        <form action="{{ route('guru.agenda.store') }}" method="POST" class="space-y-5 text-gray-900">
            @csrf

            <!-- Judul -->
            <div>
                <label class="block text-sm font-semibold mb-2">Judul</label>
                <input type="text" name="judul" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-900">
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-sm font-semibold mb-2">Tanggal</label>
                <input type="date" name="tanggal" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-900">
            </div>

            <!-- Mata Pelajaran -->
            <div>
                <label class="block text-sm font-semibold mb-2">Mata Pelajaran</label>
                <input type="text" name="mapel" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-900">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">Kelas</label>
                <select name="kelas" required
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-900">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k }}">{{ $k }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Guru -->
            <div>
                <label class="block text-sm font-semibold mb-2">Guru</label>
                <select name="id_guru"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-gray-900">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($guru as $g)
                        <option value="{{ $g->id_guru }}">{{ $g->nama_guru }} ({{ $g->mata_pelajaran }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('guru.agenda.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-900 hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tailwind CDN -->
<script src="https://cdn.tailwindcss.com"></script>
@endsection
