@extends('layouts.app')

@section('title', 'Detail Kelas Pelatihan')

@section('content')
    <div class="container py-4">
        @php $trainingClass = $trainingClass ?? null; @endphp
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>{{ $trainingClass->title ?? 'Kelas Pelatihan' }}</h3>
                <div class="small text-muted">{{ $trainingClass->start_at?->format('d F Y') ?? '-' }} —
                    {{ $trainingClass->end_at?->format('d F Y') ?? '' }}
                </div>
            </div>
            <div>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">Kembali</a>

                <a href="{{ route('teacher.training-class.materials', $trainingClass->id) }}" class="btn btn-primary">Kelola
                    Materi</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header bg-warning">
                        <strong>Daftar Peserta ({{ $trainingClass->students->count() ?? 0 }})</strong>
                        <div class="float-end small text-muted">Kapasitas: {{ $trainingClass->capacity ?? 0 }}</div>
                    </div>
                    <div class="card-body bg-light">
                        @if($students && $students->count() > 0)
                            <div class="row">
                                @foreach($students as $student)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start mb-2">
                                                    <div class="me-3">
                                                        @if($student->user->profile_photo)
                                                            <img src="{{ Storage::url($student->user->profile_photo) }}"
                                                                class="rounded-circle" width="50" height="50">
                                                        @else
                                                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                                style="width:50px;height:50px;">
                                                                <i class="fas fa-user text-white"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $student->user->name }}</h6>
                                                        <div class="small text-muted">{{ $student->student_id ?? '-' }} —
                                                            {{ $student->nisn ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span
                                                        class="badge {{ $student->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($student->status) }}</span>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('teacher.students.detail', $student->id) }}"
                                                            class="btn btn-outline-light btn-icon"><i
                                                                class="bi bi-eye text-white"></i></a>
                                                        <button type="button" class="btn btn-outline-success btn-icon"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#addGradeModal{{ $student->id }}">
                                                            <i class="bi bi-plus-lg text-white"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-info btn-icon"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#addSkillModal{{ $student->id }}">
                                                            <i class="bi bi-star text-white"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @include('teacher.partials.modals.add-grade', ['student' => $student])
                                    @include('teacher.partials.modals.add-skill', ['student' => $student])
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">Belum ada peserta di pelatihan ini.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header"><strong>Informasi Pelatihan</strong></div>
                    <div class="card-body">
                        <div class="mb-2"><strong>Pelatih</strong>
                            <div class="small text-muted">{{ optional($trainingClass->trainer)->name ?? '-' }}</div>
                        </div>
                        <div class="mb-2"><strong>Kapasitas</strong>
                            <div class="small text-muted">{{ $trainingClass->students->count() ?? 0 }} /
                                {{ $trainingClass->capacity ?? 0 }}
                            </div>
                        </div>
                        <div class="mb-2"><strong>Deskripsi</strong>
                            <div class="small text-muted">{{ $trainingClass->description ?? '-' }}</div>
                        </div>
                        <div class="mb-2"><strong>Periode</strong>
                            <div class="small text-muted">{{ $trainingClass->start_at?->format('d F Y') ?? '-' }} —
                                {{ $trainingClass->end_at?->format('d F Y') ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection