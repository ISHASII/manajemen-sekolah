@extends('layouts.app')

@section('title', 'Detail Kelas Pelatihan')

@section('content')
    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- Training Class Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">{{ $trainingClass->title }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Deskripsi:</strong><br>{{ $trainingClass->description ?: '-' }}</p>
                                <p><strong>Trainer:</strong> {{ $trainingClass->trainer?->user?->name ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Periode:</strong><br>
                                    {{ $trainingClass->start_at ? $trainingClass->start_at->translatedFormat('d M Y') : '-' }}
                                    -
                                    {{ $trainingClass->end_at ? $trainingClass->end_at->translatedFormat('d M Y') : '-' }}
                                </p>
                                <p><strong>Kuota:</strong> {{ $trainingClass->capacity ?? 'Tidak terbatas' }}</p>
                                <p><strong>Peserta Terdaftar:</strong> {{ $trainingClass->students->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Current Participants -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Peserta Terdaftar ({{ $trainingClass->students->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($trainingClass->students->count())
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Tanggal Daftar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($trainingClass->students as $student)
                                            <tr>
                                                <td>{{ $student->user?->name ?? '-' }}</td>
                                                <td>{{ $student->user?->email ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-success">{{ ucfirst($student->pivot->status) }}</span>
                                                </td>
                                                <td>{{ $student->pivot->enrolled_at ? \Carbon\Carbon::parse($student->pivot->enrolled_at)->translatedFormat('d M Y') : '-' }}
                                                </td>
                                                <td>
                                                    <form method="POST"
                                                        action="{{ route('admin.training-classes.remove-participant', $trainingClass->id) }}"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger text-dark"
                                                            style="color:#000!important;"
                                                            onclick="return confirm('Hapus peserta ini dari kelas?')">
                                                            </i> Hapus
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-people fa-2x text-muted mb-2"></i>
                                <p class="text-muted">Belum ada peserta terdaftar</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Add Participant -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Tambah Peserta</h5>
                    </div>
                    <div class="card-body">
                        @if($availableStudents->count())
                            <form method="POST"
                                action="{{ route('admin.training-classes.add-participant', $trainingClass->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Pilih Siswa Kejuruan</label>
                                    <select name="student_id" id="student_id" class="form-control" required>
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach($availableStudents as $student)
                                            <option value="{{ $student->id }}">
                                                {{ $student->user?->name }} ({{ $student->user?->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success w-100 text-dark" style="color:#000!important;">
                                    <i class="bi bi-plus-lg"></i> Tambah Peserta
                                </button>
                            </form>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-info-circle fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Tidak ada siswa kejuruan yang tersedia</p>
                                <small class="text-muted">Semua siswa kejuruan sudah terdaftar di kelas pelatihan lain</small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Statistik</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">{{ $trainingClass->students->count() }}</h4>
                                <small class="text-muted">Peserta</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">{{ $availableStudents->count() }}</h4>
                                <small class="text-muted">Tersedia</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.training-classes.edit', $trainingClass->id) }}"
                                class="btn btn-outline-primary text-dark" style="color:#ffffff!important;">
                                <i class="bi bi-pencil"></i> Edit Kelas
                            </a>
                            <a href="{{ route('admin.training-classes.index') }}"
                                class="btn btn-outline-secondary text-dark" style="color:#ffffff!important;">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection