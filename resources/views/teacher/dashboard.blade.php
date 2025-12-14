@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
    <div class="teacher-page-wrapper">
        @php use Illuminate\Support\Str; @endphp
        <div class="container-fluid py-4">
            @php
                $allSchedules = isset($allSchedules) ? $allSchedules : collect();
                $allSchedulesCount = isset($allSchedulesCount) ? $allSchedulesCount : 0;
            @endphp
            <!-- Header Dashboard -->
            <div class="row mb-4">
                @php $classes = collect($classes ?? []); @endphp
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
                                        {{ \Carbon\Carbon::now()->translatedFormat('l') }},
                                        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
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
                                    <p class="text-muted small mb-1">Total Jadwal</p>
                                    <h3 class="text-warning mb-0">{{ $allSchedulesCount ?? 0 }}</h3>
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
                                    <i class="bi bi-calendar-week me-2 text-success"></i>
                                    Jadwal Mengajar
                                </h5>
                                <span class="badge bg-success">{{ $allSchedulesCount }} Jadwal</span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($allSchedules && $allSchedules->count() > 0)
                                <div class="timeline">
                                    @foreach($allSchedules as $schedule)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div class="grow">
                                                        <h6 class="mb-1" style="color: black;">{{ $schedule->subject->name }}</h6>
                                                        <div class="row g-2">
                                                            <div class="col-md-6">
                                                                <p class="text-dark small mb-1">
                                                                    <i class="bi bi-calendar-week me-1"></i>
                                                                    Hari:
                                                                    {{ \Carbon\Carbon::create()->next($schedule->day_of_week)->translatedFormat('l') }}
                                                                </p>
                                                                <p class="text-dark small mb-1">
                                                                    <i class="bi bi-people me-1"></i>
                                                                    Kelas: {{ optional($schedule->classRoom)->name }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="text-dark small mb-1 text-end">
                                                                    <i class="bi bi-clock me-1"></i>
                                                                    <span class="text-nowrap">
                                                                        Waktu:
                                                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                                        -
                                                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                                    </span>
                                                                </p>
                                                                <p class="text-dark small mb-1">
                                                                    <i class="bi bi-geo-alt me-1"></i>
                                                                    Ruang: {{ $schedule->room ?? 'Tidak ditentukan' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <span class="badge bg-primary text-dark">
                                                            <i class="bi bi-clock me-1"></i>
                                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @if($schedule->description)
                                                    <p class="small mb-0 text-dark">{{ $schedule->description }}</p>
                                                @endif
                                                <div class="mt-2">
                                                    <a href="{{ route('teacher.schedules') }}" class="btn btn-sm btn-primary me-2">
                                                        <i class="bi bi-check2-square me-1"></i>Info Kelas
                                                    </a>
                                                    <a href="{{ route('teacher.students') }}" class="btn btn-sm btn-success">
                                                        <i class="bi bi-plus-circle me-1"></i>Input Nilai
                                                    </a> <a href="{{ route('teacher.attendance.index') }}"
                                                        class="btn btn-sm btn-primary ms-2">
                                                        <i class="bi bi-list-check me-1"></i>Rekap Absensi
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-dark mt-2">Tidak ada jadwal mengajar</p>
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
                                <a href="{{ route('teacher.profile') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-person-gear me-2"></i>Edit Profil
                                </a>
                                <a href="{{ route('teacher.graduation') }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-mortarboard me-2"></i>Kelulusan Siswa
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kelas yang Diampu -->
                    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                        <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0;">
                            <h5 class="mb-0 d-flex align-items-center" style="font-weight: 600;">
                                <i class="bi bi-collection me-2 text-primary" style="font-size: 1.2rem;"></i>
                                Kelas yang Diampu
                            </h5>
                        </div>

                        <div class="card-body">
                            @if($classes && $classes->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($classes as $class)
                                        <div class="list-group-item border-0 d-flex justify-content-between align-items-center"
                                            style="
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                padding: 14px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                margin-bottom: 8px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                border-radius: 10px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                background: white;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                box-shadow: 0 2px 6px rgba(0,0,0,0.08);
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                transition: 0.2s;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                            "
                                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'"
                                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,0.08)'">
                                            <div>
                                                <h6 class="mb-1" style="font-weight: 600; color:#222;">{{ $class->name }}</h6>
                                                <small class="text-muted" style="font-size: 0.85rem;">
                                                    <i class="bi bi-people me-1"></i>
                                                    {{ $class->students_count ?? 0 }} siswa
                                                </small>
                                            </div>

                                            <div class="d-flex gap-2">
                                                <a href="{{ route('teacher.class.attendance', $class->id) }}"
                                                    class="btn btn-sm btn-primary"
                                                    style="border-radius: 20px; padding: 4px 14px; font-size: 0.85rem;">Rekap</a>
                                                <a href="{{ route('teacher.class.detail', $class->id) }}"
                                                    class="btn btn-sm btn-warning"
                                                    style="border-radius: 20px; padding: 4px 14px; font-size: 0.85rem;">
                                                    <i></i> Lihat
                                                </a>
                                                <a href="{{ route('teacher.class.materials', $class->id) }}"
                                                    class="btn btn-sm btn-warning"
                                                    style="border-radius: 20px; padding: 4px 14px; font-size: 0.85rem;">
                                                    <i class=""></i> Kelola Materi
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if(isset($trainingClasses) && $trainingClasses->count() > 0)
                                    <hr class="my-3" />
                                    <h6 class="mb-2">Kelas Pelatihan yang Anda Latih</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($trainingClasses as $tc)
                                            <div class="list-group-item border-0 d-flex justify-content-between align-items-center"
                                                style="padding: 14px; margin-bottom: 8px; border-radius: 10px; background: white; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
                                                <div>
                                                    <h6 class="mb-1" style="font-weight: 600; color:#222;">{{ $tc->title }}</h6>
                                                    <small class="text-muted" style="font-size: 0.85rem;">{{ $tc->students_count ?? 0 }}
                                                        peserta</small>
                                                </div>
                                                <div class="d-flex gap-2"> <a
                                                        href="{{ route('teacher.training-class.attendance', $tc->id) }}"
                                                        class="btn btn-sm btn-primary"
                                                        style="border-radius: 20px; padding: 4px 14px; font-size: 0.85rem;">Rekap</a> <a
                                                        href="{{ route('teacher.training-class.detail', $tc->id) }}"
                                                        class="btn btn-sm btn-warning"
                                                        style="border-radius: 20px; padding: 4px 14px; font-size: 0.85rem;">Lihat</a>
                                                    <a href="{{ route('teacher.training-class.materials', $tc->id) }}"
                                                        class="btn btn-sm btn-warning"
                                                        style="border-radius: 20px; padding: 4px 14px; font-size: 0.85rem;">Kelola
                                                        Materi</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-3">
                                    <i class="bi bi-collection text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2 small">Belum ada kelas yang diampu</p>
                                </div>
                            @endif
                        </div>
                    </div>


                    <!-- Pengumuman Terbaru -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="mb-0">
                                <i class="bi bi-megaphone me-2 text-info"></i>
                                Pengumuman Terbaru
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($announcements) && $announcements->count() > 0)
                                @php $teacherCarouselId = 'teacherAnnouncementsCarousel'; @endphp
                                <div id="{{ $teacherCarouselId }}" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-indicators">
                                        @foreach($announcements->take(10) as $idx => $announcement)
                                            <button type="button" data-bs-target="#{{ $teacherCarouselId }}"
                                                data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}"
                                                aria-current="true" aria-label="Slide {{ $idx + 1 }}"></button>
                                        @endforeach
                                    </div>
                                    <div class="carousel-inner">
                                        @foreach($announcements->take(10) as $idx => $announcement)
                                            <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                                <a href="{{ route('announcements.show', $announcement->id) }}"
                                                    class="d-block text-decoration-none text-reset">
                                                    @if($announcement->image && Storage::disk('public')->exists($announcement->image))
                                                        <img src="{{ Storage::url($announcement->image) }}" class="d-block w-100"
                                                            alt="{{ $announcement->title }}" style="height: 140px; object-fit: cover;">
                                                    @else
                                                        <div class="d-block w-100 bg-light d-flex align-items-center justify-content-center"
                                                            style="height: 140px;">
                                                            <i class="bi bi-megaphone text-muted fs-2"></i>
                                                        </div>
                                                    @endif
                                                    <div class="carousel-caption d-none d-md-block text-start"
                                                        style="background: rgba(0,0,0,0.35); left:0; right:0; padding: .5rem;">
                                                        <h6 class="mb-0">{{ $announcement->title }}</h6>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="carousel-control-prev" type="button"
                                        data-bs-target="#{{ $teacherCarouselId }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button"
                                        data-bs-target="#{{ $teacherCarouselId }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.announcements.index') }}"
                                        class="btn btn-outline-primary btn-sm">Lihat Semua Pengumuman</a>
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

        /* Announcement card styling */
        .announcement-card .card-img-top {
            height: 90px;
            object-fit: cover;
        }

        .announcement-card .card-body {
            padding: 0.5rem;
        }

        /* Carousel specific styling for announcements */
        #teacherAnnouncementsCarousel .carousel-item img {
            height: 140px;
            object-fit: cover;
        }

        #teacherAnnouncementsCarousel .carousel-caption {
            background: rgba(0, 0, 0, 0.35);
            left: 0;
            right: 0;
            padding: .5rem;
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