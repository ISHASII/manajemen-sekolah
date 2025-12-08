@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
    <div class="teacher-page-wrapper">
        <div class="container-fluid py-4">
            @php
                $todaySchedules = isset($todaySchedules) ? $todaySchedules : collect();
                $todaySchedulesCount = isset($todaySchedulesCount) ? $todaySchedulesCount : 0;
            @endphp
            <!-- Header Dashboard -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="mb-1">
                                        <i class="bi bi-person-badge me-2"></i>
                                        Selamat Datang, {{ Auth::user()->name }}!
                                    </h2>
                                    <p class="mb-0 opacity-75">
                                        <i class="bi bi-calendar-check me-2"></i>
                                        {{ \Carbon\Carbon::now()->format('l, d F Y') }}
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="d-flex flex-column align-items-end">
                                        <span class="badge bg-light text-success fs-6 mb-1">
                                            NIP: {{ $teacher->nip ?? $teacher->teacher_id ?? '-' }}
                                        </span>
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
                                    <p class="text-muted small mb-1">Total Kelas</p>
                                    <h3 class="text-primary mb-0">{{ $totalClasses ?? 0 }}</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-primary text-white rounded-circle">
                                        <i class="bi bi-people-fill"></i>
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
                                    <p class="text-muted small mb-1">Total Siswa</p>
                                    <h3 class="text-info mb-0">{{ $totalStudents ?? 0 }}</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle">
                                        <i class="bi bi-person-lines-fill"></i>
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
                                    <p class="text-muted small mb-1">Jadwal Hari Ini</p>
                                    <h3 class="text-warning mb-0">{{ $todaySchedulesCount ?? 0 }}</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-warning text-white rounded-circle">
                                        <i class="bi bi-calendar-day"></i>
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
                                    <p class="text-muted small mb-1">Nilai Belum Input</p>
                                    <h3 class="text-danger mb-0">{{ $pendingGrades ?? 0 }}</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle">
                                        <i class="bi bi-clipboard-x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Jadwal Mengajar Hari Ini -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-calendar-day me-2 text-success"></i>
                                    Jadwal Mengajar Hari Ini
                                </h5>
                                <span class="badge bg-success">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($todaySchedules && $todaySchedules->count() > 0)
                                <div class="timeline">
                                    @foreach($todaySchedules as $schedule)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1">{{ $schedule->subject->name }}</h6>
                                                        <p class="text-muted small mb-1">
                                                            <i class="bi bi-people me-1"></i>
                                                            Kelas: {{ optional($schedule->classRoom)->name }}
                                                        </p>
                                                    </div>
                                                    <span class="badge bg-light text-dark">
                                                        {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                                    </span>
                                                </div>
                                                @if($schedule->description)
                                                    <p class="small mb-0 text-muted">{{ $schedule->description }}</p>
                                                @endif
                                                <div class="mt-2">
                                                    <a href="{{ route('teacher.schedules') }}"
                                                        class="btn btn-sm btn-outline-primary me-2">
                                                        <i class="bi bi-check2-square me-1"></i>Absensi
                                                    </a>
                                                    <a href="{{ route('teacher.students') }}"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-plus-circle me-1"></i>Input Nilai
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">Tidak ada jadwal mengajar untuk hari ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Info -->
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
                                <a href="{{ route('teacher.schedules') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-calendar-week me-2"></i>Kelola Jadwal
                                </a>
                                <a href="{{ route('teacher.students') }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-people me-2"></i>Daftar Siswa
                                </a>
                                <a href="{{ route('teacher.grades.manage') }}" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-bar-chart me-2"></i>Kelola Nilai
                                </a>
                                <a href="{{ route('teacher.announcements.create') }}"
                                    class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-megaphone me-2"></i>Buat Pengumuman
                                </a>
                                <a href="{{ route('teacher.profile') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-person-gear me-2"></i>Edit Profil
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kelas yang Diampu -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bi bi-collection me-2 text-primary"></i>
                                Kelas yang Diampu
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($classes && $classes->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($classes as $class)
                                        <div
                                            class="list-group-item px-0 border-0 border-bottom d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">{{ $class->name }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-people me-1"></i>
                                                    {{ $class->students_count ?? 0 }} siswa
                                                </small>
                                            </div>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('teacher.class.detail', $class->id) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="bi bi-collection text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2 small">Belum ada kelas yang diampu</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Nilai dan Kehadiran -->
            <div class="row mt-4">
                <!-- Grafik Nilai -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up me-2 text-info"></i>
                                Statistik Nilai Siswa
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="gradesChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Daftar Siswa dengan Nilai Rendah -->
                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-exclamation-triangle me-2 text-warning"></i>
                                    Perhatian Khusus
                                </h5>
                                <span class="badge bg-warning text-dark">{{ $lowGradesStudents->count() ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($lowGradesStudents && $lowGradesStudents->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Siswa</th>
                                                <th>Kelas</th>
                                                <th>Rata-rata</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lowGradesStudents->take(5) as $student)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div
                                                                class="avatar-sm bg-warning rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                                <i class="bi bi-person text-white"></i>
                                                            </div>
                                                            <strong>{{ $student->user->name }}</strong>
                                                        </div>
                                                    </td>
                                                    <td>{{ optional($student->classRoom)->name }}</td>
                                                    <td>
                                                        <span class="badge bg-danger">
                                                            {{ number_format($student->average_grade, 1) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('teacher.student.detail', $student->id) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('teacher.students.low-grades') }}" class="btn btn-outline-warning btn-sm">
                                        Lihat Semua
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                    <p class="text-success mt-2">Semua siswa memiliki nilai yang baik!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history me-2 text-secondary"></i>
                                Aktivitas Terbaru
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($recentActivities && $recentActivities->count() > 0)
                                <div class="timeline-activities">
                                    @foreach($recentActivities->take(5) as $activity)
                                        <div class="activity-item d-flex mb-3">
                                            <div
                                                class="activity-icon bg-{{ $activity->type === 'grade' ? 'success' : ($activity->type === 'announcement' ? 'info' : 'primary') }} text-white rounded-circle me-3">
                                                <i
                                                    class="bi bi-{{ $activity->type === 'grade' ? 'clipboard-check' : ($activity->type === 'announcement' ? 'megaphone' : 'calendar-check') }}"></i>
                                            </div>
                                            <div class="activity-content">
                                                <p class="mb-1">{{ $activity->description }}</p>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $activity->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-2">Belum ada aktivitas terbaru</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Page wrapper to scope a white background to main content only */
        .teacher-page-wrapper {
            background: #ffffff;
            /* White background only for the main page content */
            min-height: 100vh;
            /* Ensure the background covers full viewport content area */
            /* Do not change card-specific colors; wrapper only changes page background */
        }

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
            border-left: 3px solid #198754;
        }

        .activity-icon {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .avatar-sm {
            width: 2rem;
            height: 2rem;
            font-size: 0.75rem;
        }

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        /* Make the header of each card orange inside teacher pages without changing card body colors
                                                           -- also override any Bootstrap `bg-white` utility class using `!important` to ensure this
                                                           styling applies when markup uses `bg-white` on the header. */
        .teacher-page-wrapper .card-header,
        .teacher-page-wrapper .card-header.bg-white {
            background-color: #fd7e14 !important;
            /* Force orange background */
            color: #ffffff !important;
            /* White text for contrast */
            border-bottom-color: rgba(0, 0, 0, 0.05) !important;
        }

        /* Ensure header titles keep readable color */
        .teacher-page-wrapper .card-header h5,
        .teacher-page-wrapper .card-header .mb-0 {
            color: #ffffff !important;
        }

        /* Make sure small header badges or icons are still visible (use higher specificity) */
        .teacher-page-wrapper .card-header .badge,
        .teacher-page-wrapper .card-header .small,
        .teacher-page-wrapper .card-header .bi {
            color: #ffffff !important;
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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Grafik Nilai
            const ctx = document.getElementById('gradesChart').getContext('2d');
            const gradesChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['A (90-100)', 'B (80-89)', 'C (70-79)', 'D (60-69)', 'E (<60)'],
                    datasets: [{
                        data: [
                                                                                            {{ $gradeDistribution['A'] ?? 0 }},
                                                                                            {{ $gradeDistribution['B'] ?? 0 }},
                                                                                            {{ $gradeDistribution['C'] ?? 0 }},
                                                                                            {{ $gradeDistribution['D'] ?? 0 }},
                            {{ $gradeDistribution['E'] ?? 0 }}
                        ],
                        backgroundColor: [
                            '#198754',
                            '#0dcaf0',
                            '#ffc107',
                            '#fd7e14',
                            '#dc3545'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    }
                }
            });

            // Animasi counter
            const counters = document.querySelectorAll('.card-body h3');
            counters.forEach(counter => {
                const target = parseInt(counter.textContent) || 0;
                let current = 0;
                const increment = target / 20;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 50);
            });
        });
    </script>
@endpush