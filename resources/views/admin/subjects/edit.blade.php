@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran')

@section('content')
    <div class="container py-4">
        <h3>Edit Mata Pelajaran</h3>
        <div class="card mt-3 p-4">
            <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="">- Pilih -</option>
                            <option value="academic" {{ old('category', $subject->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="vocational" {{ old('category', $subject->category) == 'vocational' ? 'selected' : '' }}>Vocational</option>
                            <option value="extracurricular" {{ old('category', $subject->category) == 'extracurricular' ? 'selected' : '' }}>Extracurricular</option>
                        </select>
                        required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="">-- Pilih Kategori --</option>
                            <option value="academic" {{ old('category', $subject->category) == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="vocational" {{ old('category', $subject->category) == 'vocational' ? 'selected' : '' }}>Vocational</option>
                            <option value="extracurricular" {{ old('category', $subject->category) == 'extracurricular' ? 'selected' : '' }}>Extracurricular</option>
                        </select>
                        @error('category') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">SKS</label>
                        <input type="number" name="credit_hours" class="form-control"
                            value="{{ old('credit_hours', $subject->credit_hours) ?? 0 }}">
                        @error('credit_hours') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kategori</label>
                        <input type="text" name="category" class="form-control"
                            value="{{ old('category', $subject->category) }}">
                        @error('category') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $subject->is_active) == 1 ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="0" {{ old('is_active', $subject->is_active) == 0 ? 'selected' : '' }}>Nonaktif
                            </option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $subject->description) }}</textarea>
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