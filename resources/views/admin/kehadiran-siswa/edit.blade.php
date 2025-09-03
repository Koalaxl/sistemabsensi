@extends('layouts.admin.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-white border-0 p-4">
                    <h5 class="mb-0">Edit Kehadiran Siswa</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.kehadiran-siswa.update', $kehadiran_siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Siswa</label>
                            <select name="siswa_id" class="form-select" required>
                                @foreach($siswa as $s)
                                    <option value="{{ $s->id }}" {{ $kehadiran_siswa->siswa_id == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_siswa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ $kehadiran_siswa->tanggal }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach($listStatus as $status)
                                    <option value="{{ $status }}" {{ $kehadiran_siswa->status == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2">{{ $kehadiran_siswa->keterangan }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Update</button>
                        <a href="{{ route('admin.kehadiran-siswa.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
