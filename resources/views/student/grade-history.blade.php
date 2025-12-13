@extends('layouts.app')

@section('title', 'Riwayat Nilai & Kelas')

@section('content')
    <div class="grade-history-page">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Riwayat Nilai & Kelas Lengkap</h2>
                <a href="{{ auth()->user()->role === 'kejuruan' ? route('kejuruan.dashboard') : route('student.dashboard') }}"
                    class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Student Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-badge"></i> Informasi Siswa</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama:</strong> {{ auth()->user()->name }}</p>
                            <p><strong>NISN:</strong> {{ $student->nisn ?? '-' }}</p>
                            <p><strong>Status:</strong>
                                @if (auth()->user()->role === 'kejuruan')
                                    <span class="badge bg-success">Alumni (Kejuruan)</span>
                                @else
                                    <span class="badge bg-info">{{ ucfirst($student->status) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Kelas Saat Ini:</strong>
                                {{ $student->classRoom ? $student->classRoom->name : 'Belum ada kelas' }}</p>
                            <p><strong>Tanggal Masuk:</strong>
                                {{ $student->enrollment_date ? \Carbon\Carbon::parse($student->enrollment_date)->translatedFormat('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade History Timeline -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Kelas & Nilai Per Semester</h5>
                </div>
                <div class="card-body">
                    @if ($gradeHistory->isEmpty())
                        <p class="text-muted">Belum ada riwayat nilai yang tersimpan.</p>
                    @else
                        <div class="timeline">
                            @foreach ($gradeHistory as $history)
                                <div
                                    class="card mb-3 border-{{ $history->status == 'passed' ? 'success' : ($history->status == 'failed' ? 'danger' : 'secondary') }}">
                                    <div
                                        class="card-header bg-{{ $history->status == 'passed' ? 'success' : ($history->status == 'failed' ? 'danger' : 'secondary') }} text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="bi bi-mortarboard"></i>
                                                {{ $history->class_name }} - Tahun {{ $history->academic_year }} Semester
                                                {{ $history->semester }}
                                            </h6>
                                            <span class="badge bg-light text-dark">
                                                @if ($history->status == 'passed')
                                                    <i class="bi bi-check-circle"></i> Lulus
                                                @elseif($history->status == 'failed')
                                                    <i class="bi bi-x-circle"></i> Tidak Lulus
                                                @else
                                                    <i class="bi bi-hourglass-split"></i> Dalam Proses
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong>Rata-rata Nilai:</strong>
                                                    <span class="badge bg-primary fs-6">
                                                        {{ $history->average_grade ? number_format($history->average_grade, 2) : 'N/A' }}
                                                    </span>
                                                </p>
                                                <p><strong>Tanggal Selesai:</strong>
                                                    {{ $history->completed_at ? $history->completed_at->translatedFormat('d M Y') : '-' }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                @if ($history->notes)
                                                    <p><strong>Catatan:</strong> {{ $history->notes }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($history->subjects_grades && count($history->subjects_grades) > 0)
                                            <h6 class="mb-2"><i class="bi bi-book"></i> Detail Nilai Per Mata Pelajaran:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Mata Pelajaran</th>
                                                            <th>Nilai</th>
                                                            <th>Catatan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($history->subjects_grades as $index => $grade)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $grade['subject'] ?? '-' }}</td>
                                                                <td>
                                                                    <span
                                                                        class="badge bg-{{ $grade['score'] >= 75 ? 'success' : ($grade['score'] >= 60 ? 'warning' : 'danger') }}">
                                                                        {{ $grade['score'] ?? 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td>{{ $grade['notes'] ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Current Grades -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Nilai Semester Aktif</h5>
                </div>
                <div class="card-body">
                    @if ($currentGrades->isEmpty())
                        <p class="text-muted">Belum ada nilai untuk semester aktif.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Nilai</th>
                                        <th>Semester</th>
                                        <th>Tanggal Penilaian</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($currentGrades as $index => $grade)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $grade->subject->name }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $grade->score >= 75 ? 'success' : ($grade->score >= 60 ? 'warning' : 'danger') }}">
                                                    {{ $grade->score }}
                                                </span>
                                            </td>
                                            <td>Semester {{ $grade->semester }}</td>
                                            <td>{{ $grade->assessment_date ? $grade->assessment_date->translatedFormat('d M Y') : '-' }}
                                            </td>
                                            <td>{{ $grade->notes ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <p><strong>Rata-rata Nilai Semester Aktif:</strong>
                                <span class="badge bg-primary fs-6">
                                    {{ number_format($currentGrades->avg('score'), 2) }}
                                </span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Training History (for Kejuruan only) -->
            @if (auth()->user()->role === 'kejuruan' && count($trainingHistory) > 0)
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-trophy"></i> Riwayat Pelatihan Kejuruan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pelatihan</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal Mulai</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($trainingHistory as $index => $training)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $training->title }}</td>
                                            <td>{{ Str::limit($training->description, 50) }}</td>
                                            <td>{{ $training->start_date ? $training->start_date->translatedFormat('d M Y') : '-' }}
                                            </td>
                                            <td>{{ $training->end_date ? $training->end_date->translatedFormat('d M Y') : '-' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $training->pivot->status == 'completed' ? 'success' : 'info' }}">
                                                    {{ ucfirst($training->pivot->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .grade-history-page {
            background: #ffffff !important;
        }

        .timeline {
            position: relative;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
