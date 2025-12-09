@extends('layouts.app')

@section('title', 'Kelola Nilai')

@section('content')
    <div class="teacher-page-wrapper">
        <div class="container-fluid py-4">
            @php $classes = collect($classes ?? []); @endphp
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-info border-bottom">
                            <h5 class="mb-0">Kelola Nilai</h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <form method="GET" action="{{ route('teacher.grades.manage') }}"
                                class="row g-2 align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label small">Kelas</label>
                                    <select name="class_id" class="form-select">
                                        <option value="">-- Pilih Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ (int) ($selectedClass ?? 0) === $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Mata Pelajaran</label>
                                    <select name="subject_id" class="form-select">
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                        @foreach($subjects as $id => $name)
                                            <option value="{{ $id }}" {{ (string) ($selectedSubject ?? '') === (string) $id ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Jenis Penilaian</label>
                                    <select name="assessment_type" class="form-select">
                                        <option value="daily">Harian</option>
                                        <option value="midterm">UTS</option>
                                        <option value="final">UAS</option>
                                        <option value="project">Proyek</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Semester</label>
                                    <input type="text" name="semester" value="{{ request('semester', '') }}"
                                        class="form-control" placeholder="Contoh: Ganjil 2024/2025">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Tanggal</label>
                                    <input type="date" name="assessment_date"
                                        value="{{ request('assessment_date', now()->format('Y-m-d')) }}"
                                        class="form-control">
                                </div>
                                <div class="col-md-12 mt-2">
                                    <button type="submit" class="btn btn-primary btn-sm">Muat Siswa</button>
                                    <a href="{{ route('teacher.dashboard') }}"
                                        class="btn btn-outline-secondary btn-sm">Kembali</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if($classes->count() === 0)
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">Belum ada kelas yang Anda ampuh. Silakan buat kelas di admin atau minta
                            admin menugaskan Anda.</div>
                    </div>
                </div>
            @endif

            @if($students && $students->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="mb-0">Input Nilai - {{ $classes->firstWhere('id', $selectedClass)->name ?? '' }} -
                                    {{ $subjects[$selectedSubject] ?? '' }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('teacher.grades.manage.store') }}">
                                    @csrf
                                    <input type="hidden" name="class_id" value="{{ $selectedClass }}" />
                                    <input type="hidden" name="subject_id" value="{{ $selectedSubject }}" />
                                    <input type="hidden" name="assessment_type" value="{{ request('assessment_type') }}" />
                                    <input type="hidden" name="semester" value="{{ request('semester') }}" />
                                    <input type="hidden" name="assessment_date" value="{{ request('assessment_date') }}" />
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama Siswa</th>
                                                    <th>NISN</th>
                                                    <th>Nilai</th>
                                                    <th>Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($students as $student)
                                                    <tr>
                                                        <td>{{ $student->user->name }}</td>
                                                        <td>{{ $student->nisn ?? '-' }}</td>
                                                        <td>
                                                            <input type="number" step="0.1" min="0" max="100"
                                                                name="scores[{{ $student->id }}]" class="form-control" value="" />
                                                        </td>
                                                        <td><input type="text" name="notes[{{ $student->id }}]"
                                                                class="form-control" /></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success">Simpan Semua</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
