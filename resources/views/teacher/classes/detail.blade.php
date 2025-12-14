@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
    <div class="container py-4">
        @php $classRoom = $classRoom ?? null; @endphp
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3>{{ $classRoom->name ?? 'Kelas' }}</h3>
                <div class="small text-muted">{{ $classRoom->grade_level ?? '-' }} — {{ $classRoom->description ?? '' }}
                </div>
            </div>
            <div>
                <a href="{{ route('teacher.class.attendance', $classRoom->id) }}" class="btn btn-primary">Rekap
                    Absensi</a>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header bg-warning">
                        <strong>Daftar Siswa ({{ $classRoom->students->count() ?? 0 }})</strong>
                        <div class="float-end small text-muted">Kapasitas: {{ $classRoom->capacity ?? 0 }}</div>
                    </div>
                    <div class="card-body bg-light">
                        @if($classRoom->students && $classRoom->students->count() > 0)
                            <div class="mb-3 d-flex justify-content-end">
                                <button class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#bulkAttendanceModalClass{{ $classRoom->id }}">Absensi</button>
                                <a href="{{ route('teacher.class.attendance', $classRoom->id) }}"
                                    class="btn btn-primary ms-2">Rekap Absensi</a>
                            </div>

                            <!-- Bulk Attendance Modal -->
                            <div class="modal fade" id="bulkAttendanceModalClass{{ $classRoom->id }}" tabindex="-1"
                                aria-labelledby="bulkAttendanceModalClass{{ $classRoom->id }}Label" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('teacher.attendance.bulk') }}">
                                            @csrf
                                            <input type="hidden" name="class_id" value="{{ $classRoom->id }}">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="bulkAttendanceModalClass{{ $classRoom->id }}Label">
                                                    Absensi - {{ $classRoom->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-2 align-items-end">
                                                    <div class="col-auto">
                                                        <label class="form-label">Tanggal</label>
                                                        <input type="date" name="date" value="{{ now()->format('Y-m-d') }}"
                                                            class="form-control">
                                                    </div>
                                                    <div class="col-auto">
                                                        <label class="form-label">Mata Pelajaran</label>
                                                        <select name="subject_id" class="form-select">
                                                            <option value="">-- Pilih Mata Pelajaran --</option>
                                                            @foreach($subjects as $sub)
                                                                <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="table-responsive mt-3">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Nama</th>
                                                                <th>Status</th>
                                                                <th>Catatan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($classRoom->students as $idx => $student)
                                                                <tr>
                                                                    <td>{{ $idx + 1 }}</td>
                                                                    <td>{{ $student->user->name }}</td>
                                                                    <td>
                                                                        <select name="students[{{ $student->id }}][status]"
                                                                            class="form-select">
                                                                            <option value="">--</option>
                                                                            <option value="present">Hadir</option>
                                                                            <option value="absent">Alpha</option>
                                                                            <option value="sick">Sakit</option>
                                                                            <option value="excused">Izin</option>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text"
                                                                            name="students[{{ $student->id }}][notes]"
                                                                            class="form-control" placeholder="Catatan (opsional)">
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Simpan Absensi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                @foreach($classRoom->students as $student)
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
                                                    <div>
                                                        <span
                                                            class="badge {{ $student->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($student->status) }}</span>
                                                        @if($student->latestAttendance)
                                                            <div class="small text-muted mt-1">Terakhir:
                                                                {{ $student->latestAttendance->date->translatedFormat('d F Y') }} —
                                                                <span
                                                                    class="badge bg-info">{{ ucfirst($student->latestAttendance->status) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex align-items-center">

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
                                    </div>
                                    @include('teacher.partials.modals.add-grade', ['student' => $student])
                                    @include('teacher.partials.modals.add-skill', ['student' => $student])
                                    {{-- per-student attendance modal removed; use bulk modal instead --}}
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">Belum ada siswa di kelas ini.</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header"><strong>Informasi Kelas</strong></div>
                    <div class="card-body">
                        <div class="mb-2"><strong>Wali Kelas</strong>
                            <div class="small text-muted">{{ optional($classRoom->homeroomTeacher)->name ?? '-' }}</div>
                        </div>
                        <div class="mb-2"><strong>Kapasitas</strong>
                            <div class="small text-muted">{{ $classRoom->current_students ?? 0 }} /
                                {{ $classRoom->capacity ?? 0 }}
                            </div>
                        </div>
                        <div class="mb-2"><strong>Deskripsi</strong>
                            <div class="small text-muted">{{ $classRoom->description ?? '-' }}</div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><strong>Jadwal (Mata Pelajaran)</strong></div>
                    <div class="card-body">
                        @if(isset($schedules) && $schedules->count())
                            <ul class="list-group list-group-flush">
                                @foreach($schedules as $sched)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small"><strong>{{ $sched->subject?->name ?? '-' }}</strong></div>
                                            <div class="small text-muted">
                                                {{ $sched->day_of_week ? \Carbon\Carbon::create()->next($sched->day_of_week)->translatedFormat('l') : '' }}
                                                —
                                                {{ optional($sched->start_time)->format('H:i') ?? '' }} sampai
                                                {{ optional($sched->end_time)->format('H:i') ?? '' }}
                                            </div>
                                        </div>
                                        <div class="small text-muted">{{ optional($sched->teacher)->name ?? '-' }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="text-muted small">Tidak ada jadwal untuk kelas ini.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection