@extends('layouts.admin')

@section('title', 'Edit Guru')

@section('admin-content')
    <div class="container py-4">
        <h3>Edit Guru</h3>
        <div class="card mt-3 p-4">
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
            <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->user->name) }}"
                            required>
                        @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $teacher->user->email) }}" required>
                        @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teacher ID</label>
                        <input type="text" name="teacher_id" class="form-control"
                            value="{{ old('teacher_id', $teacher->teacher_id) }}" required>
                        @error('teacher_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">NIP</label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $teacher->nip) }}">
                        @error('nip') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="subjects" class="form-select">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($subjects as $subject)
                                @php
                                    $selectedValue = old('subjects', isset($teacher->subjects[0]) ? $teacher->subjects[0] : null);
                                @endphp
                                <option value="{{ $subject->id }}" {{ (string) $selectedValue === (string) $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('subjects') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Masuk</label>
                        <input type="date" name="hire_date" class="form-control"
                            value="{{ old('hire_date', $teacher->hire_date?->toDateString()) }}" required>
                        @error('hire_date') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="inactive" {{ old('status', $teacher->status) == 'inactive' ? 'selected' : '' }}>
                                Nonaktif</option>
                            <option value="retired" {{ old('status', $teacher->status) == 'retired' ? 'selected' : '' }}>
                                Pensiun</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection