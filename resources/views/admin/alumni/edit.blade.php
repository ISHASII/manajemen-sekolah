@extends('layouts.app')

@section('title', 'Edit Alumni')

@section('content')
    <div class="container py-4">
        <h3>Edit Alumni</h3>
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
            <form action="{{ route('admin.alumni.update', $alumni->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Siswa</label>
                    @if(isset($students) && count($students) > 0)
                        <select name="student_id" class="form-select" required>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" {{ old('student_id', $alumni->student_id) == $s->id ? 'selected' : '' }}>
                                    {{ optional($s->user)->name ?? 'ID-' . $s->id }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <div class="alert alert-warning mb-0">Belum ada data siswa. Tambahkan siswa dulu sebelum menambah
                            alumni.</div>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Kelulusan</label>
                    <input type="date" name="graduation_date" class="form-control"
                        value="{{ old('graduation_date', $alumni->graduation_date?->toDateString()) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kelas Kelulusan (Opsional)</label>
                    <input type="text" name="graduation_class" class="form-control"
                        value="{{ old('graduation_class', $alumni->graduation_class) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pekerjaan Saat Ini (Opsional)</label>
                    <input type="text" name="current_job" class="form-control"
                        value="{{ old('current_job', $alumni->current_job) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Perusahaan Saat Ini (Opsional)</label>
                    <input type="text" name="current_company" class="form-control"
                        value="{{ old('current_company', $alumni->current_company) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">LinkedIn / CV Online (Opsional)</label>
                    <input type="url" name="linkedin_profile" class="form-control"
                        value="{{ old('linkedin_profile', $alumni->linkedin_profile) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Skills (pisahkan dengan koma)</label>
                    <input type="text" name="skills" class="form-control"
                        value="{{ old('skills', is_array($alumni->skills) ? implode(',', $alumni->skills) : $alumni->skills) }}">
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection