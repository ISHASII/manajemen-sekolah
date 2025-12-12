@extends('layouts.app')

@section('title', 'Manajemen Kelulusan')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen Kelulusan Siswa</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @foreach ($classes as $class)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kelas: {{ $class->name }}</h5>
                </div>
                <div class="card-body">
                    @if ($class->students->isEmpty())
                        <p class="text-muted">Tidak ada siswa di kelas ini.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>NISN</th>
                                        <th>Rata-rata Nilai</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($class->students as $index => $student)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $student->user->name }}</td>
                                            <td>{{ $student->nisn ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $avgGrade = $student->grades->avg('score');
                                                @endphp
                                                {{ $avgGrade ? number_format($avgGrade, 2) : 'Belum ada nilai' }}
                                            </td>
                                            <td>
                                                @if ($student->status == 'active')
                                                    <span class="badge bg-success">Aktif</span>
                                                @elseif($student->status == 'graduated')
                                                    <span class="badge bg-primary">Lulus</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($student->status == 'active')
                                                    <button type="button" class="btn btn-sm btn-success text-dark"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#graduationModal{{ $student->id }}">
                                                        <i class="bi bi-check-circle"></i> Proses Kelulusan
                                                    </button>
                                                @else
                                                    <span class="text-muted">Sudah diproses</span>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Graduation Modal -->
                                        <div class="modal fade" id="graduationModal{{ $student->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('teacher.graduation.process', $student->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Proses Kelulusan -
                                                                {{ $student->user->name }}</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Status Kelulusan *</label>
                                                                <select name="status" class="form-select" required>
                                                                    <option value="">Pilih Status</option>
                                                                    <option value="passed">Lulus</option>
                                                                    <option value="failed">Tidak Lulus</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Tahun Ajaran *</label>
                                                                <input type="number" name="academic_year"
                                                                    class="form-control" value="{{ date('Y') }}"
                                                                    min="2020" max="2100" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Semester *</label>
                                                                <select name="semester" class="form-select" required>
                                                                    <option value="">Pilih Semester</option>
                                                                    <option value="1">Semester 1</option>
                                                                    <option value="2">Semester 2</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Catatan</label>
                                                                <textarea name="notes" class="form-control" rows="3"
                                                                    placeholder="Catatan tambahan (opsional)"></textarea>
                                                            </div>

                                                            <div class="alert alert-info">
                                                                <small>
                                                                    <strong>Info:</strong><br>
                                                                    - Jika <strong>Lulus</strong>, siswa akan naik ke kelas
                                                                    berikutnya<br>
                                                                    - Siswa kelas 3 SMA yang lulus akan menjadi role
                                                                    <strong>Kejuruan</strong><br>
                                                                    - Jika <strong>Tidak Lulus</strong>, siswa tetap di
                                                                    kelas yang sama
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Proses</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        @if ($classes->isEmpty() && (!isset($trainingClasses) || $trainingClasses->isEmpty()))
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Anda belum menjadi wali kelas untuk kelas manapun.
            </div>
        @endif

        {{-- Training classes that this teacher trains --}}
        @if (isset($trainingClasses) && $trainingClasses->count() > 0)
            @foreach ($trainingClasses as $training)
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Pelatihan: {{ $training->title }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($training->students->isEmpty())
                            <p class="text-muted">Tidak ada peserta di pelatihan ini.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Peserta</th>
                                            <th>NISN</th>
                                            <th>Rata-rata Nilai</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($training->students as $idx => $student)
                                            <tr>
                                                <td>{{ $idx + 1 }}</td>
                                                <td>{{ $student->user->name }}</td>
                                                <td>{{ $student->nisn ?? '-' }}</td>
                                                <td>
                                                    @php
                                                        $avgGrade = $student->grades->avg('score');
                                                    @endphp
                                                    {{ $avgGrade ? number_format($avgGrade, 2) : 'Belum ada nilai' }}
                                                </td>
                                                <td>
                                                    @if ($student->status == 'active')
                                                        <span class="badge bg-success">Aktif</span>
                                                    @elseif($student->status == 'graduated')
                                                        <span class="badge bg-primary">Lulus</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst($student->status) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($student->status == 'active')
                                                        <button type="button" class="btn btn-sm btn-success text-dark" data-bs-toggle="modal" data-bs-target="#graduationModalTraining{{ $student->id }}">
                                                            <i class="bi bi-check-circle"></i> Proses Kelulusan
                                                        </button>
                                                    @else
                                                        <span class="text-muted">Sudah diproses</span>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Training Graduation Modal -->
                                            <div class="modal fade" id="graduationModalTraining{{ $student->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('teacher.graduation.process', $student->id) }}">
                                                            @csrf
                                                            <input type="hidden" name="training_class_id" value="{{ $training->id }}">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Proses Kelulusan - {{ $student->user->name }} (Pelatihan: {{ $training->title }})</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Status Kelulusan *</label>
                                                                    <select name="status" class="form-select" required>
                                                                        <option value="">Pilih Status</option>
                                                                        <option value="passed">Lulus</option>
                                                                        <option value="failed">Tidak Lulus</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Tahun Ajaran *</label>
                                                                    <input type="number" name="academic_year" class="form-control" value="{{ date('Y') }}" min="2020" max="2100" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Semester *</label>
                                                                    <select name="semester" class="form-select" required>
                                                                        <option value="">Pilih Semester</option>
                                                                        <option value="1">Semester 1</option>
                                                                        <option value="2">Semester 2</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Catatan</label>
                                                                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                                                                </div>
                                                                <div class="alert alert-info">
                                                                    <small>
                                                                        <strong>Info:</strong><br>
                                                                        - Jika <strong>Lulus</strong>, peserta pelatihan akan ditandai sebagai lulus/Alumni<br>
                                                                        - Jika <strong>Tidak Lulus</strong>, peserta tetap berstatus aktif
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Proses</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection
