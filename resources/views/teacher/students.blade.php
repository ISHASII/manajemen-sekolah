@extends('layouts.app')

@section('title', 'Daftar Siswa')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Daftar Siswa</h2>
                    <div>
                        <span class="text-muted">Total: {{ $classes->sum('current_students') }} siswa</span>
                    </div>
                </div>

                @if($classes->count() > 0)
                    @foreach($classes as $class)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h5 class="mb-0">{{ $class->name }}</h5>
                                        <small>{{ $class->description ?: $class->grade_level }}</small>
                                    </div>
                                    <div class="col-auto">
                                        <span class="badge bg-light text-dark">
                                            {{ $class->current_students }}/{{ $class->capacity }} siswa
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if($class->students->count() > 0)
                                    <div class="row">
                                        @foreach($class->students as $student)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border h-100">
                                                    <div class="card-body">
                                                        <div class="d-flex align-items-start mb-3">
                                                            <div class="me-3">
                                                                @if($student->user->profile_photo)
                                                                    <img src="{{ Storage::url($student->user->profile_photo) }}"
                                                                        class="rounded-circle" width="50" height="50">
                                                                @else
                                                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                                                        style="width: 50px; height: 50px;">
                                                                        <i class="fas fa-user text-white"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="flex-grow-1">
                                                                <h6 class="mb-1">{{ $student->user->name }}</h6>
                                                                <small class="text-muted d-block">{{ $student->student_id }}</small>
                                                                <small class="text-muted d-block">{{ $student->user->email }}</small>
                                                            </div>
                                                        </div>

                                                        <div class="mb-2">
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                Bergabung: {{ $student->enrollment_date->format('M Y') }}
                                                            </small>
                                                        </div>

                                                        @if($student->is_orphan)
                                                            <div class="mb-2">
                                                                <span class="badge bg-info">Yatim Piatu</span>
                                                            </div>
                                                        @endif

                                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                                            <span class="badge
                                                                                @if($student->status === 'active') bg-success
                                                                                @else bg-secondary
                                                                                @endif">
                                                                {{ ucfirst($student->status) }}
                                                            </span>

                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ route('teacher.students.detail', $student->id) }}"
                                                                    class="btn btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <button class="btn btn-outline-success" data-bs-toggle="modal"
                                                                    data-bs-target="#addGradeModal{{ $student->id }}">
                                                                    <i class="fas fa-plus"></i> Nilai
                                                                </button>
                                                                <button class="btn btn-outline-info" data-bs-toggle="modal"
                                                                    data-bs-target="#addSkillModal{{ $student->id }}">
                                                                    <i class="fas fa-star"></i> Skill
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Add Grade Modal -->
                                            <div class="modal fade" id="addGradeModal{{ $student->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('teacher.grades.store') }}">
                                                            @csrf
                                                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Tambah Nilai - {{ $student->user->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="subject_id{{ $student->id }}" class="form-label">Mata
                                                                        Pelajaran</label>
                                                                    <select name="subject_id" id="subject_id{{ $student->id }}"
                                                                        class="form-control" required>
                                                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                                                        @foreach(App\Models\Subject::where('is_active', true)->get() as $subject)
                                                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="score{{ $student->id }}"
                                                                                class="form-label">Nilai</label>
                                                                            <input type="number" name="score" id="score{{ $student->id }}"
                                                                                class="form-control" min="0" max="100" step="0.1" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="assessment_type{{ $student->id }}"
                                                                                class="form-label">Jenis Penilaian</label>
                                                                            <select name="assessment_type"
                                                                                id="assessment_type{{ $student->id }}" class="form-control"
                                                                                required>
                                                                                <option value="daily">Harian</option>
                                                                                <option value="midterm">UTS</option>
                                                                                <option value="final">UAS</option>
                                                                                <option value="project">Proyek</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="semester{{ $student->id }}"
                                                                        class="form-label">Semester</label>
                                                                    <input type="text" name="semester" id="semester{{ $student->id }}"
                                                                        class="form-control" placeholder="Contoh: Ganjil 2024/2025"
                                                                        required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="assessment_date{{ $student->id }}"
                                                                        class="form-label">Tanggal Penilaian</label>
                                                                    <input type="date" name="assessment_date"
                                                                        id="assessment_date{{ $student->id }}" class="form-control"
                                                                        required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="notes{{ $student->id }}" class="form-label">Catatan
                                                                        (Opsional)</label>
                                                                    <textarea name="notes" id="notes{{ $student->id }}" class="form-control"
                                                                        rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan Nilai</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Add Skill Modal -->
                                            <div class="modal fade" id="addSkillModal{{ $student->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('teacher.skills.store') }}">
                                                            @csrf
                                                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Penilaian Keterampilan - {{ $student->user->name }}
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="skill_name{{ $student->id }}" class="form-label">Nama
                                                                        Keterampilan</label>
                                                                    <input type="text" name="skill_name" id="skill_name{{ $student->id }}"
                                                                        class="form-control" required>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="skill_category{{ $student->id }}"
                                                                                class="form-label">Kategori</label>
                                                                            <select name="skill_category"
                                                                                id="skill_category{{ $student->id }}" class="form-control"
                                                                                required>
                                                                                <option value="academic">Akademik</option>
                                                                                <option value="technical">Teknis</option>
                                                                                <option value="soft_skill">Soft Skill</option>
                                                                                <option value="language">Bahasa</option>
                                                                                <option value="art">Seni</option>
                                                                                <option value="sport">Olahraga</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label for="proficiency_level{{ $student->id }}"
                                                                                class="form-label">Level Kemampuan</label>
                                                                            <select name="proficiency_level"
                                                                                id="proficiency_level{{ $student->id }}"
                                                                                class="form-control" required>
                                                                                <option value="beginner">Pemula</option>
                                                                                <option value="intermediate">Menengah</option>
                                                                                <option value="advanced">Mahir</option>
                                                                                <option value="expert">Ahli</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="skill_description{{ $student->id }}"
                                                                        class="form-label">Deskripsi</label>
                                                                    <textarea name="description" id="skill_description{{ $student->id }}"
                                                                        class="form-control" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Simpan Penilaian</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">Tidak ada siswa di kelas ini</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada kelas</h5>
                            <p class="text-muted">Anda belum ditugaskan mengajar di kelas manapun</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection