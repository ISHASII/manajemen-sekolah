@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header Dashboard -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-1">
                                    <i class="bi bi-person-circle me-2"></i>
                                    Selamat Datang, {{ Auth::user()->name }}!
                                </h2>
                                <p class="mb-0 opacity-75">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    {{ \Carbon\Carbon::now()->format('l, d F Y') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <span class="badge bg-light text-primary fs-6 mb-1">
                                        {{ $student->nis }}
                                    </span>
                                    <small class="opacity-75">
                                        Kelas: {{ $student->classRoom->name ?? 'Belum ditentukan' }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="text-muted small mb-1">Kehadiran Bulan Ini</p>
                                <h3 class="text-success mb-0">{{ $attendancePercent ?? 0 }}%</h3>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-success text-white rounded-circle">
                                    <i class="bi bi-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="text-muted small mb-1">Rata-rata Nilai</p>
                                <h3 class="text-info mb-0">{{ number_format($averageGrade ?? 0, 1) }}</h3>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-info text-white rounded-circle">
                                    <i class="bi bi-graph-up"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="text-muted small mb-1">Tugas Pending</p>
                                <h3 class="text-warning mb-0">{{ $pendingAssignments ?? 0 }}</h3>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-warning text-white rounded-circle">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="text-muted small mb-1">Keterampilan</p>
                                <h3 class="text-primary mb-0">{{ $student->skills->count() ?? 0 }}</h3>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-primary text-white rounded-circle">
                                    <i class="bi bi-star-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Jadwal Hari Ini -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-day me-2 text-primary"></i>
                                Jadwal Hari Ini
                            </h5>
                            <span class="badge bg-primary">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($todaySchedules && $todaySchedules->count() > 0)
                            <div class="timeline">
                                @foreach($todaySchedules as $schedule)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1">{{ $schedule->subject->name }}</h6>
                                                    <p class="text-muted small mb-1">
                                                        <i class="bi bi-person me-1"></i>
                                                        {{ $schedule->teacher->user->name }}
                                                    </p>
                                                </div>
                                                <span class="badge bg-light text-dark">
                                                    {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                                </span>
                                            </div>
                                            @if($schedule->description)
                                                <p class="small mb-0 text-muted">{{ $schedule->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Tidak ada jadwal untuk hari ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pengumuman & Quick Actions -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning-fill me-2 text-warning"></i>
                            Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('student.schedule') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-calendar-week me-2"></i>Lihat Jadwal Lengkap
                            </a>
                            <a href="{{ route('student.grades') }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-bar-chart me-2"></i>Lihat Nilai
                            </a>
                            <a href="{{ route('student.profile') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-person-gear me-2"></i>Edit Profil
                            </a>
                            <a href="{{ route('student.skills') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-award me-2"></i>Keterampilan Saya
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pengumuman Terbaru -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="bi bi-megaphone me-2 text-info"></i>
                            Pengumuman Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(!empty($announcements) && $announcements->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($announcements->take(3) as $announcement)
                                    <div class="list-group-item px-0 border-0 border-bottom">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $announcement->title }}</h6>
                                                <p class="mb-1 small text-muted">
                                                    {{ Str::limit($announcement->content, 80) }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    {{ $announcement->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            @if($announcement->is_important)
                                                <span class="badge bg-danger ms-2">Penting</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('student.announcements') }}" class="btn btn-outline-primary btn-sm">
                                    Lihat Semua Pengumuman
                                </a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-megaphone text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 small">Belum ada pengumuman</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Nilai Terbaru -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-clipboard-data me-2 text-success"></i>
                                Nilai Terbaru
                            </h5>
                            <a href="{{ route('student.grades') }}" class="btn btn-outline-primary btn-sm">
                                Lihat Semua Nilai
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($recentGrades && $recentGrades->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mata Pelajaran</th>
                                            <th>Jenis</th>
                                            <th>Nilai</th>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentGrades->take(5) as $grade)
                                            <tr>
                                                <td>
                                                    <strong>{{ $grade->subject->name }}</strong>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $grade->type === 'UTS' ? 'warning' : ($grade->type === 'UAS' ? 'danger' : 'info') }}">
                                                        {{ $grade->type }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $grade->value >= 80 ? 'success' : ($grade->value >= 70 ? 'warning' : 'danger') }} fs-6">
                                                        {{ $grade->value }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $grade->date->format('d/m/Y') }}</small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $grade->description ?? '-' }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Belum ada nilai yang tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .timeline {
            position: relative;
            padding-left: 1.5rem;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: -1.75rem;
            top: 0.25rem;
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: -1.375rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #e9ecef;
        }

        .timeline-content {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 3px solid #0d6efd;
        }

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        .list-group-item {
            transition: background-color 0.2s ease-in-out;
        }

        .list-group-item:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .timeline {
                padding-left: 1rem;
            }

            .timeline-marker {
                left: -1.25rem;
            }

            .timeline::before {
                left: -0.875rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto refresh untuk data real-time (opsional)
            // setInterval(function() {
            //     location.reload();
            // }, 300000); // Refresh setiap 5 menit

            // Animasi counter
            const counters = document.querySelectorAll('.card-body h3');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent) || parseFloat(counter.textContent) || 0;
                let current = 0;
                const increment = target / 20;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target.toString().includes('.') ? target.toFixed(1) : target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = target.toString().includes('.') ? current.toFixed(1) : Math.floor(current);
                    }
                }, 50);
            });

            // Tooltip untuk badge status
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush