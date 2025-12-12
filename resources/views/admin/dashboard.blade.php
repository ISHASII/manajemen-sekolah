@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
    <style>
        /* Scoped admin dashboard styles - only affects this view */
        .admin-page-wrapper {
            min-height: 100vh;
            background: #ffffff;
            color: #111827;
        }

        /* Ensure cards have white background and dark text for admin pages */
        .admin-page-wrapper .card {
            background: #ffffff !important;
            color: #111827 !important;
            border: 1px solid rgba(0, 0, 0, 0.06) !important;
        }

        /* Make the icon circles yellow and the icons themselves dark (black) */
        .admin-page-wrapper .icon.icon-shape,
        .admin-page-wrapper .icon.icon-shape.rounded-circle,
        .admin-page-wrapper .icon.icon-shape.bg-warning {
            background: #ffd700 !important;
            /* yellow */
            color: #000 !important;
        }

        .admin-page-wrapper .icon.icon-shape i,
        .admin-page-wrapper .icon.icon-shape .bi,
        .admin-page-wrapper .icon.icon-shape svg {
            color: #000 !important;
            fill: #000 !important;
        }

        /* Header / card header style change for admin wrapper */
        .admin-page-wrapper .card-header {
            background: #ffffff !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        /* Make sure links on admin view are readable */
        .admin-page-wrapper .nav-link,
        .admin-page-wrapper a {
            color: #111827 !important;
        }

        /* Force all common text elements inside admin wrapper to dark (black) while keeping layout navbar/footer untouched */
        .admin-page-wrapper,
        .admin-page-wrapper p,
        .admin-page-wrapper small,
        .admin-page-wrapper span,
        .admin-page-wrapper label,
        .admin-page-wrapper li,
        .admin-page-wrapper td,
        .admin-page-wrapper th,
        .admin-page-wrapper h1,
        .admin-page-wrapper h2,
        .admin-page-wrapper h3,
        .admin-page-wrapper h4,
        .admin-page-wrapper h5,
        .admin-page-wrapper h6,
        .admin-page-wrapper a,
        .admin-page-wrapper strong,
        .admin-page-wrapper b,
        .admin-page-wrapper em,
        .admin-page-wrapper .card-title,
        .admin-page-wrapper .card-text,
        .admin-page-wrapper .text-muted,
        .admin-page-wrapper .text-primary,
        .admin-page-wrapper .text-success,
        .admin-page-wrapper .text-info,
        .admin-page-wrapper .text-warning,
        .admin-page-wrapper .text-danger {
            color: #111827 !important;
        }

        /* Icons in circle shapes must remain white for contrast */
        .admin-page-wrapper .icon.icon-shape,
        .admin-page-wrapper .icon.icon-shape i,
        .admin-page-wrapper .icon.icon-shape .bi,
        .admin-page-wrapper .icon.icon-shape svg {
            color: #fff !important;
            fill: #fff !important;
        }

        /* Action buttons inside the recent applications table: icons should be black */
        .admin-page-wrapper .recent-applications-table .btn i,
        .admin-page-wrapper .recent-applications-table .btn .bi {
            color: #111827 !important;
            fill: #111827 !important;
        }

        /* Note: navbar/footer styles are kept as layout defaults. No body-level overrides here. */
        /* Dashboard header icon (shield) should be orange */
        .admin-page-wrapper .dashboard-header .dashboard-header-icon,
        .admin-page-wrapper .dashboard-header-icon {
            color: var(--orange) !important;
            fill: var(--orange) !important;
        }
    </style>
@endpush

@section('admin-content')
    <div class="container-fluid py-4 admin-page-wrapper">
        <!-- Header Dashboard -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg text-dark">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-1 dashboard-header">
                                    <i class="bi bi-shield-check me-2 dashboard-header-icon"></i>
                                    Dashboard Administrator
                                </h2>
                                <p class="mb-0 opacity-75">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    {{ $daysInIndonesian[strtolower(\Carbon\Carbon::now()->format('l'))] ?? \Carbon\Carbon::now()->format('l') }},
                                    {{ \Carbon\Carbon::now()->format('d F Y') }}
                                </p>
                                <p class="mb-0 opacity-50 small">
                                    Selamat datang, {{ Auth::user()->name }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <span class="badge bg-light text-dark fs-6 mb-1">
                                        Administrator
                                    </span>
                                    <small class="opacity-75">
                                        Akses Penuh Sistem
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <p class="text-muted small mb-1">Total Siswa</p>
                                <h3 class="text-primary mb-0">{{ $totalStudents ?? 0 }}</h3>
                                <small class="text-success">
                                    <i class="bi bi-arrow-up"></i> {{ $newStudentsThisMonth ?? 0 }} baru bulan ini
                                </small>
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
                                <p class="text-muted small mb-1">Total Guru</p>
                                <h3 class="text-success mb-0">{{ $totalTeachers ?? 0 }}</h3>
                                <small class="text-info">
                                    <i class="bi bi-person-check"></i> {{ $activeTeachers ?? 0 }} aktif
                                </small>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-success text-white rounded-circle">
                                    <i class="bi bi-person-badge-fill"></i>
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
                                <p class="text-muted small mb-1">Total Kelas</p>
                                <h3 class="text-info mb-0">{{ $totalClasses ?? 0 }}</h3>
                                <small class="text-primary">
                                    <i class="bi bi-collection"></i> {{ $totalSubjects ?? 0 }} mata pelajaran
                                </small>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-info text-white rounded-circle">
                                    <i class="bi bi-building"></i>
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
                                <p class="text-muted small mb-1">Pendaftar Baru</p>
                                <h3 class="text-warning mb-0">{{ $pendingApplications ?? 0 }}</h3>
                                <small class="text-danger">
                                    <i class="bi bi-clock"></i> Menunggu Verifikasi
                                </small>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-warning text-white rounded-circle">
                                    <i class="bi bi-person-plus-fill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Quick Actions -->
            <div class="col-lg-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning-fill me-2 text-warning"></i>
                            Menu Utama
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Manajemen User -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card border text-center h-100">
                                    <div class="card-body">
                                        <div class="icon icon-shape bg-primary text-white rounded-circle mx-auto mb-3">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <h6 class="card-title">Manajemen User</h6>
                                        <p class="card-text small text-muted">Kelola siswa, guru, dan admin</p>
                                        <div class="btn-group-vertical w-100" role="group">
                                            <a href="{{ route('admin.students.index') }}"
                                                class="btn btn-outline-primary btn-sm">Kelola Siswa</a>
                                            <a href="{{ route('admin.teachers.index') }}"
                                                class="btn btn-outline-success btn-sm">Kelola Guru</a>
                                            <a href="{{ route('admin.users.index') }}"
                                                class="btn btn-outline-info btn-sm">Semua User</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Akademik -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card border text-center h-100">
                                    <div class="card-body">
                                        <div class="icon icon-shape bg-success text-white rounded-circle mx-auto mb-3">
                                            <i class="bi bi-book-fill"></i>
                                        </div>
                                        <h6 class="card-title">Akademik</h6>
                                        <p class="card-text small text-muted">Kelola kelas dan mata pelajaran</p>
                                        <div class="btn-group-vertical w-100" role="group">
                                            <a href="{{ route('admin.classes.index') }}"
                                                class="btn btn-outline-primary btn-sm">Kelola Kelas</a>
                                            <a href="{{ route('admin.training-classes.index') }}"
                                                class="btn btn-outline-primary btn-sm">Kelola Pelatihan</a>
                                            <a href="{{ route('admin.subjects.index') }}"
                                                class="btn btn-outline-success btn-sm">Mata Pelajaran</a>
                                            <a href="{{ route('admin.schedules.index') }}"
                                                class="btn btn-outline-info btn-sm">Jadwal</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pendaftaran -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card border text-center h-100">
                                    <div class="card-body">
                                        <div class="icon icon-shape bg-warning text-white rounded-circle mx-auto mb-3">
                                            <i class="bi bi-person-plus-fill"></i>
                                        </div>
                                        <h6 class="card-title">Pendaftaran</h6>
                                        <p class="card-text small text-muted">Verifikasi pendaftaran baru</p>
                                        <div class="btn-group-vertical w-100" role="group">
                                            <a href="{{ route('admin.applications.pending') }}"
                                                class="btn btn-outline-warning btn-sm">
                                                Pending ({{ $pendingApplications ?? 0 }})
                                            </a>
                                            <a href="{{ route('admin.applications.index') }}"
                                                class="btn btn-outline-info btn-sm">Semua Pendaftar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Komunikasi -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card border text-center h-100">
                                    <div class="card-body">
                                        <div class="icon icon-shape bg-info text-white rounded-circle mx-auto mb-3">
                                            <i class="bi bi-megaphone-fill"></i>
                                        </div>
                                        <h6 class="card-title">Komunikasi</h6>
                                        <p class="card-text small text-muted">Pengumuman dan informasi</p>
                                        <div class="btn-group-vertical w-100" role="group">
                                            <a href="{{ route('admin.announcements.create') }}"
                                                class="btn btn-outline-primary btn-sm">Buat Pengumuman</a>
                                            <a href="{{ route('admin.announcements.index') }}"
                                                class="btn btn-outline-info btn-sm">Kelola Pengumuman</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Alumni -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card border text-center h-100">
                                    <div class="card-body">
                                        <div class="icon icon-shape bg-secondary text-white rounded-circle mx-auto mb-3">
                                            <i class="bi bi-mortarboard-fill"></i>
                                        </div>
                                        <h6 class="card-title">Alumni</h6>
                                        <p class="card-text small text-muted">Data lulusan sekolah</p>
                                        <div class="btn-group-vertical w-100" role="group">
                                            <a href="{{ route('admin.alumni.index') }}"
                                                class="btn btn-outline-secondary btn-sm">Data Alumni</a>
                                            <a href="{{ route('admin.alumni.create') }}"
                                                class="btn btn-outline-primary btn-sm">Tambah Alumni</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sistem -->
                            <div class="col-md-6 col-lg-4">
                                <div class="card border text-center h-100">
                                    <div class="card-body">
                                        <div class="icon icon-shape bg-primary text-white rounded-circle mx-auto mb-3">
                                            <i class="bi bi-gear-fill"></i>
                                        </div>
                                        <h6 class="card-title">Sistem</h6>
                                        <p class="card-text small text-muted">Pengaturan sekolah</p>
                                        <div class="btn-group-vertical w-100" role="group">
                                            <a href="{{ route('admin.school.edit') }}"
                                                class="btn btn-outline-primary btn-sm">Info Sekolah</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics -->
    <div class="row mt-4">
        <!-- Grafik Pendaftaran -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2 text-success"></i>
                        Statistik Pendaftaran (6 Bulan Terakhir)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="registrationChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Distribusi Kelas -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart me-2 text-info"></i>
                        Distribusi Siswa per Kelas
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="classDistributionChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pendaftar Terbaru -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person-lines-fill me-2 text-primary"></i>
                            Pendaftar Terbaru
                        </h5>
                        <a href="{{ route('admin.applications.index') }}" class="btn btn-outline-primary btn-sm">
                            Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentApplications && $recentApplications->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover recent-applications-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Kelas Tujuan</th>
                                        <th>Status</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentApplications->take(10) as $application)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-person text-white"></i>
                                                    </div>
                                                    <strong>{{ $application->full_name }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $application->email }}</td>
                                            <td>{{ $application->desired_class ?? '-' }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $application->status === 'pending' ? 'warning' : ($application->status === 'approved' ? 'success' : 'danger') }}">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small
                                                    class="text-muted">{{ $application->created_at->format('d/m/Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.applications.detail', $application->id) }}"
                                                        class="btn btn-outline-primary btn-sm btn-detail">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($application->status === 'pending')
                                                        <a href="{{ route('admin.applications.approve', $application->id) }}"
                                                            class="btn btn-outline-success btn-sm">
                                                            <i class="bi bi-check"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-plus text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Belum ada pendaftar baru</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
    {{-- No direct body class changes; layout navbar/footer remain unchanged --}}
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

        .activity-icon {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
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

        .btn-group-vertical .btn {
            margin-bottom: 0.25rem;
        }

        .btn-group-vertical .btn:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Grafik Pendaftaran
            const regCtx = document.getElementById('registrationChart').getContext('2d');
            const registrationChart = new Chart(regCtx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach($registrationChartData['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'] as $label)
                            '{{ $label }}',
                        @endforeach
                                                                                                                        ],
                    datasets: [{
                        label: 'Pendaftaran',
                        data: [
                            @foreach($registrationChartData['data'] ?? [10, 15, 8, 22, 18, 12] as $data)
                                {{ $data }},
                            @endforeach
                                                                                                                            ],
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Grafik Distribusi Kelas
            const classCtx = document.getElementById('classDistributionChart').getContext('2d');
            const classDistributionChart = new Chart(classCtx, {
                type: 'doughnut',
                data: {
                    labels: [
                        @foreach($classDistributionData['labels'] ?? ['Kelas X', 'Kelas XI', 'Kelas XII'] as $label)
                            '{{ $label }}',
                        @endforeach
                                                                                                                        ],
                    datasets: [{
                        data: [
                            @foreach($classDistributionData['data'] ?? [45, 38, 42] as $data)
                                {{ $data }},
                            @endforeach
                                                                                                                            ],
                        backgroundColor: [
                            '#0d6efd',
                            '#198754',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1',
                            '#20c997'
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
                const increment = target / 30;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 30);
            });

            // Auto refresh untuk data real-time
            setInterval(function () {
                // Update hanya statistik kecil tanpa reload page
                fetch('{{ route("admin.dashboard.stats") }}')
                    .then(response => response.json())
                    .then(data => {
                        // Update counters jika diperlukan
                        console.log('Stats updated:', data);
                    });
            }, 30000); // Update setiap 30 detik
        });
    </script>
@endpush