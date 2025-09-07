@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="mb-0 text-primary">Data Guru</h3>
            <p class="text-secondary">Kelola data guru dengan mudah.</p>
        </div>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Tambah Data --}}
    <div class="mb-4">
        <a href="{{ route('admin.guru.create') }}" class="btn btn-primary shadow-sm rounded-3">
            <i class="bi bi-plus-lg"></i> Tambah Guru
        </a>
    </div>
    {{-- Filter & Search --}}
    <div class="card shadow border-0 mb-4 rounded-4">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="mb-0">Filter, Pencarian & Opsi Manajemen</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">

                {{-- Filter Mata Pelajaran --}}
                <form action="{{ route('admin.guru.index') }}" method="GET" class="row g-3 align-items-end col-md-6">
                    <div class="col-md-8">
                        <label class="form-label text-muted">Mata Pelajaran</label>
                        <select name="mata_pelajaran" class="form-select rounded-3">
                            <option value="">-- Semua Mata Pelajaran --</option>
                            @foreach($listMataPelajaran as $mp)
                                <option value="{{ $mp }}" {{ request('mata_pelajaran') == $mp ? 'selected' : '' }}>
                                    {{ $mp }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" class="btn btn-info rounded-3">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                </form>

                {{-- Search Nama/NIP/Mapel --}}
                <form action="{{ route('admin.guru.index') }}" method="GET" class="row g-3 align-items-end col-md-6">
                    <div class="col-md-8">
                        <label class="form-label text-muted">Cari Guru</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control rounded-3"
                            placeholder="Nama, NIP, atau Mata Pelajaran">
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" class="btn btn-primary rounded-3">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>
                 {{-- Hapus Semua Guru di Mata Pelajaran yg difilter --}}
                @if(request('mata_pelajaran'))
                <div class="col-md-3 d-grid">
                    <form action="{{ route('guru.destroyByMataPelajaran') }}" method="POST"
                        onsubmit="return confirm('Yakin hapus semua guru mata pelajaran {{ request("mata_pelajaran") }}?')">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="mata_pelajaran" value="{{ request('mata_pelajaran') }}">
                        <button type="submit" class="btn btn-danger rounded-3">
                            <i class="bi bi-trash"></i> Hapus by Filter
                        </button>
                    </form>
                </div>
                @endif

            </div>
            </div>
        </div>
    </div>
    </div>
    {{-- Data Guru --}}
    <div class="card shadow border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th style="width: 50px">No</th>
                            <th>Nama Guru</th>
                            <th>NIP</th>
                            <th>Mata Pelajaran</th>
                            <th style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($guru as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->nama_guru }}</td>
                            <td>{{ $item->nip }}</td>
                            <td>{{ $item->mata_pelajaran }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.guru.edit', $item) }}" class="btn btn-sm btn-warning rounded-3">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('admin.guru.destroy', $item) }}" method="POST"
                                          onsubmit="return confirm('Yakin mau hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger rounded-3">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-journal-x fs-3 d-block mb-2"></i>
                                Belum ada data guru.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Hapus Semua --}}
    <div class="mt-4">
        <form action="{{ route('guru.hapusSemua') }}" method="POST"
              onsubmit="return confirm('Yakin hapus semua data guru?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger rounded-3 shadow-sm">
                <i class="bi bi-trash3"></i> Hapus Semua
            </button>
        </form>
    </div>

    {{-- Import --}}
    <div class="mt-4">
        <div class="card shadow border-0 rounded-4">
            <div class="card-header bg-success text-white rounded-top-4">
                Import Data Guru
            </div>
            <div class="card-body">
                <form action="{{ route('guru.import') }}" method="POST" enctype="multipart/form-data" class="row g-3 align-items-center">
                    @csrf
                    <div class="col-md-8">
                        <input type="file" name="file" class="form-control rounded-3" required>
                    </div>
                    <div class="col-md-4 d-grid">
                        <button type="submit" class="btn btn-success rounded-3">
                            <i class="bi bi-upload"></i> Import
                        </button>
                    </div>
                </form>
                <small class="text-muted d-block mt-2">
                    Format file: <strong>.xlsx</strong> atau <strong>.csv</strong> — Kolom: Nama Guru, NIP, Mata Pelajaran
                </small>
            </div>
        </div>
    </div>

</div>
@endsection
