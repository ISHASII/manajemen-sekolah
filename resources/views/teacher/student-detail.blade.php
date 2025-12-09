@extends('layouts.app')

@section('title', 'Detail Siswa')

<style>
    /* Semua teks di halaman jadi hitam. NOTE: keep this scoped, button colors will be overridden below */
    .page-detail-siswa * {
        color: #000 !important;
    }

    /* Card header tetap teks putih meski warning */
    .page-detail-siswa .card-header,
    .page-detail-siswa .card-header * {
        color: #fff !important;
    }

    /* Card body putih */
    .page-detail-siswa .card-body {
        background: #ffffff !important;
    }

    /* Ensure buttons have readable text/icons on colored backgrounds despite the global color rule */
    .page-detail-siswa .btn,
    .page-detail-siswa .btn i,
    .page-detail-siswa .btn * {
        color: #fff !important;
    }
</style>

@section('content')
    <div class="container py-4 page-detail-siswa">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="text-white">
                <h3 class="text-white">{{ $student->user->name }}</h3>
                <div class="small text-white">{{ optional($student->classRoom)->name ?? '-' }}</div>
            </div>

            <div>
                <a href="{{ route('teacher.students') }}" class="btn btn-outline-secondary text-light">Kembali</a>
                <button type="button" class="btn btn-outline-success text-light" data-bs-toggle="modal"
                    data-bs-target="#addGradeModal{{ $student->id }}">Tambahkan Nilai</button>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                {{-- CARD INFORMASI SISWA --}}
                <div class="card mb-3">
                    <div class="card-header bg-warning"><strong>Informasi Siswa</strong></div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="me-3">
                                @if($student->user->profile_photo)
                                    <img src="{{ Storage::url($student->user->profile_photo) }}" class="rounded-circle"
                                        width="80" height="80">
                                @else
                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width:80px;height:80px;">
                                        <i class="fas fa-user text-dark"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $student->user->name }}</h4>
                                <div class="small">NIS: {{ $student->student_id ?? '-' }} — NISN:
                                    {{ $student->nisn ?? '-' }}
                                </div>
                                <div class="small">Email: {{ $student->user->email ?? '-' }} — Telp:
                                    {{ $student->user->phone ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Tempat, Tanggal Lahir</strong>
                                <div class="small">{{ $student->place_of_birth ?? '-' }},
                                    {{ optional($student->birth_date)->format('d M Y') ?? '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Alamat</strong>
                                <div class="small">{{ $student->address ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD NILAI --}}
                <div class="card mb-3">
                    <div class="card-header bg-warning"><strong>Nilai</strong></div>
                    <div class="card-body">
                        @if($grades->count())
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Mata Pelajaran</th>
                                            <th>Nilai</th>
                                            <th>Tipe</th>
                                            <th>Semester</th>
                                            <th>Aksi</th>
                                            <th>Tgl Penilaian</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($grades as $g)
                                            <tr>
                                                <td>{{ $g->subject?->name ?? '-' }}</td>
                                                <td><span class="badge bg-primary">{{ $g->score }}</span></td>
                                                <td>{{ ucfirst($g->assessment_type ?? '-') }}</td>
                                                <td>{{ $g->semester ?? '-' }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-primary btn-icon"
                                                            data-bs-toggle="modal" data-bs-target="#editGradeModal{{ $g->id }}">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        <form method="POST" action="{{ route('teacher.grades.destroy', $g->id) }}"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger btn-icon"
                                                                onclick="return confirm('Hapus nilai ini?')">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                                <td>{{ optional($g->assessment_date)->format('d M Y') ?? '-' }}</td>
                                            </tr>
                                            @include('teacher.partials.modals.edit-grade', ['grade' => $g])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="small">Belum ada nilai yang dicatat untuk siswa ini.</div>
                        @endif
                    </div>
                </div>

                {{-- CARD KETERAMPILAN --}}
                <div class="card">
                    <div class="card-header bg-warning"><strong>Keterampilan</strong></div>
                    <div class="card-body">
                        @if($skills->count())
                            <div class="list-group">
                                @foreach($skills as $sk)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $sk->skill_name }}</strong>
                                            <div class="small">{{ ucfirst($sk->skill_category ?? '') }} —
                                                {{ ucfirst($sk->proficiency_level ?? '') }}
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <div class="small me-3">{{ optional($sk->assessed_date)->format('d M Y') ?? '' }}</div>
                                            <div class="btn-group">
                                                <button type="button" aria-label="Edit penilaian keterampilan"
                                                    class="btn btn-sm btn-primary btn-icon" data-bs-toggle="modal"
                                                    data-bs-target="#editSkillModal{{ $sk->id }}"><i
                                                        class="bi bi-pencil-fill"></i></button>
                                                <form method="POST" action="{{ route('teacher.skills.destroy', $sk->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" aria-label="Hapus penilaian keterampilan"
                                                        class="btn btn-sm btn-danger btn-icon"
                                                        onclick="return confirm('Hapus penilaian keterampilan ini?')">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @include('teacher.partials.modals.edit-skill', ['skill' => $sk])
                                @endforeach
                            </div>
                        @else
                            <div class="small">Belum ada penilaian keterampilan.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- CARD ORANG TUA --}}
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-warning"><strong>Kontak Orang Tua</strong></div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Nama Orang Tua</strong>
                            <div class="small">{{ $student->parent_name ?? '-' }}</div>
                        </div>
                        <div class="mb-2">
                            <strong>Email Orang Tua</strong>
                            <div class="small">{{ $student->parent_email ?? '-' }}</div>
                        </div>
                        <div class="mb-2">
                            <strong>Telepon</strong>
                            <div class="small">{{ $student->parent_phone ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('teacher.partials.modals.add-grade', ['student' => $student])
        @include('teacher.partials.modals.add-skill', ['student' => $student])
    </div>
@endsection
