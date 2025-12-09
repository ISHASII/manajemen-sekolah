@extends('layouts.admin')

@section('title', 'Buat Mata Pelajaran')

@section('admin-content')
    <div class="container py-4">
        <h3>Buat Mata Pelajaran Baru</h3>
        <div class="card mt-3 p-4">
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Mata Pelajaran</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kode</label>
                        <input type="text" name="code" class="form-control" value="{{ old('code') }}" required>
                        @error('code') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SKS</label>
                        <input type="number" name="credit_hours" class="form-control"
                            value="{{ old('credit_hours') ?? 0 }}">
                        @error('credit_hours') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="">- Pilih -</option>
                            <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="vocational" {{ old('category') == 'vocational' ? 'selected' : '' }}>Vocational
                            </option>
                            <option value="extracurricular" {{ old('category') == 'extracurricular' ? 'selected' : '' }}>
                                Extracurricular</option>
                        </select>
                        @error('category') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        @error('description') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.subjects.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
