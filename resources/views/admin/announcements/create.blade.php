@extends('layouts.admin')

@section('title', 'Buat Pengumuman')

@section('admin-content')
    <div class="container py-4">
        <h3>Buat Pengumuman Baru</h3>
        <div class="card mt-3">
            <div class="card-body">
                <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($errors && $errors->any())
                        <div class="alert alert-warning">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konten</label>
                        <textarea name="content" class="form-control" rows="6">{{ old('content') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-select">
                            <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General</option>
                            <option value="academic" {{ old('type') == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>Event</option>
                            <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Publik untuk</label>
                        <select name="target_audience" class="form-select">
                            <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="students" {{ old('target_audience') == 'students' ? 'selected' : '' }}>Siswa
                            </option>
                            <option value="teachers" {{ old('target_audience') == 'teachers' ? 'selected' : '' }}>Guru
                            </option>
                            <option value="parents" {{ old('target_audience') == 'parents' ? 'selected' : '' }}>Orang Tua
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Publish</label>
                        <input type="date" name="publish_date" class="form-control"
                            value="{{ old('publish_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kadaluarsa (optional)</label>
                        <input type="date" name="expire_date" class="form-control" value="{{ old('expire_date') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Pengumuman (JPG/PNG) - opsional</label>
                        <input type="file" name="image" class="form-control">
                        @error('image') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection