@extends('layouts.admin.app')

@section('content')
<div class="container">
    <h2>Edit Wali Kelas</h2>
    <form action="{{ route('wali-kelas.update', $wali_kela->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Guru</label>
            <select name="guru_id" class="form-select" required>
                <option value="">-- Pilih Guru --</option>
                @foreach($guru as $g)
                    <option value="{{ $g->id }}" {{ $wali_kela->guru_id == $g->id ? 'selected' : '' }}>
                        {{ $g->nama_guru }} - {{ $g->mata_pelajaran }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Kelas</label>
            <input type="text" name="kelas" class="form-control" value="{{ $wali_kela->kelas }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('wali-kelas.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
