@extends('layouts.app')

@section('title', 'Buat Kelas Pelatihan')

@section('content')
    <div class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                Buat Kelas Pelatihan
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.training-classes.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mulai</label>
                        <input type="date" name="start_at" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Akhir</label>
                        <input type="date" name="end_at" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kuota</label>
                        <input type="number" name="capacity" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trainer</label>
                        <select name="trainer_id" class="form-select">
                            <option value="">-- Pilih Trainer --</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ old('trainer_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->user?->name ?? ('Guru #' . $t->id) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-check-label me-2">Buka untuk kejuruan</label>
                        <input type="checkbox" name="open_to_kejuruan" value="1" checked>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.training-classes.index') }}" class="btn btn-secondary me-2">Batal</a>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
