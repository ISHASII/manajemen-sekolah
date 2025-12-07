@extends('layouts.admin')

@section('title', 'Edit Pengumuman')

@section('admin-content')
    <div class="container py-4">
        <h3>Edit Pengumuman</h3>
        <div class="card mt-3">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors && $errors->any())
                    <div class="alert alert-warning">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" name="title" class="form-control"
                            value="{{ old('title', $announcement->title) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konten</label>
                        <textarea name="content" class="form-control"
                            rows="6">{{ old('content', $announcement->content) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-select">
                            <option value="general" {{ old('type', $announcement->type) == 'general' ? 'selected' : '' }}>
                                General</option>
                            <option value="academic" {{ old('type', $announcement->type) == 'academic' ? 'selected' : '' }}>
                                Academic</option>
                            <option value="event" {{ old('type', $announcement->type) == 'event' ? 'selected' : '' }}>Event
                            </option>
                            <option value="urgent" {{ old('type', $announcement->type) == 'urgent' ? 'selected' : '' }}>Urgent
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Publik untuk</label>
                        <select name="target_audience" class="form-select">
                            <option value="all" {{ old('target_audience', $announcement->target_audience) == 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="students" {{ old('target_audience', $announcement->target_audience) == 'students' ? 'selected' : '' }}>Siswa</option>
                            <option value="teachers" {{ old('target_audience', $announcement->target_audience) == 'teachers' ? 'selected' : '' }}>Guru</option>
                            <option value="parents" {{ old('target_audience', $announcement->target_audience) == 'parents' ? 'selected' : '' }}>Orang Tua</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Publish</label>
                        <input type="date" name="publish_date" class="form-control"
                            value="{{ old('publish_date', $announcement->publish_date?->toDateString()) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Kadaluarsa (optional)</label>
                        <input type="date" name="expire_date" class="form-control"
                            value="{{ old('expire_date', $announcement->expire_date?->toDateString()) }}">
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection