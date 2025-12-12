@extends('layouts.app')

@section('title', 'Dashboard Kejuruan')

@section('content')
    <div class="container-fluid py-4 student-page-wrapper">
        @php
            $recentGrades = isset($recentGrades) ? $recentGrades : collect();
            $trainingClasses = isset($trainingClasses) ? $trainingClasses : collect();
        @endphp
        <!-- Header Dashboard -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-1">
                                    <i class="bi bi-mortarboard-fill me-2"></i>
                                    Selamat Datang, {{ Auth::user()->name }}!
                                </h2>
                                <p class="mb-0 opacity-75">
                                    <i class="bi bi-calendar-check me-2"></i>
                                    {{ $daysInIndonesian[strtolower(\Carbon\Carbon::now()->format('l'))] ?? \Carbon\Carbon::now()->format('l') }},
                                    {{ \Carbon\Carbon::now()->format('d F Y') }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex flex-column align-items-end">
                                    <span class="badge bg-warning text-dark fs-6 mb-1">
                                        <i class="bi bi-briefcase me-1"></i> Peserta Kejuruan
                                    </span>
                                    <small class="opacity-75">
                                        {{ optional($student)->nisn ?? '-' }}
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
                                <p class="text-muted small mb-1">Pelatihan Diikuti</p>
                                <h3 class="text-success mb-0">{{ $trainingClasses->count() }}</h3>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-success text-white rounded-circle">
                                    <i class="bi bi-briefcase-fill"></i>
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
                                <p class="text-muted small mb-1">Materi Tersedia</p>
                                <h3 class="text-warning mb-0">{{ $materialsCount ?? 0 }}</h3>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-warning text-white rounded-circle">
                                    <i class="bi bi-folder-fill"></i>
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
                                <h3 class="text-primary mb-0">{{ optional(optional($student)->skills)->count() ?? 0 }}</h3>
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
            <!-- KIRI - Aksi Cepat -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header border-bottom" style="background-color: orange;">
                        <h5 class="mb-0">
                            <i class="bi bi-lightning-fill me-2 text-warning"></i>
                            Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('kejuruan.cv.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-file-earmark-person me-2"></i>Buat CV (ATS-Friendly)
                            </a>
                            <a href="{{ route('kejuruan.training-classes.index') }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-briefcase me-2"></i>Daftar Pelatihan
                            </a>
                            <a href="{{ route('kejuruan.profile') }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-person-gear me-2"></i>Edit Profil
                            </a>
                            <a href="{{ route('kejuruan.grades') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-award me-2"></i>Nilai & Keterampilan Saya
                            </a>
                            <a href="{{ route('kejuruan.grade-history') }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-clock-history me-2"></i>Riwayat Nilai Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KANAN - Pengumuman -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom" style="background-color: orange;">
                        <h5 class="mb-0">
                            <i class="bi bi-megaphone me-2 text-info"></i>
                            Pengumuman Terbaru
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(!empty($announcements) && $announcements->count() > 0)
                            @php $carouselId = 'kejuruanAnnouncementsCarousel'; @endphp
                            <div id="{{ $carouselId }}" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    @foreach($announcements->take(10) as $idx => $announcement)
                                        <button type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide-to="{{ $idx }}"
                                            class="{{ $idx === 0 ? 'active' : '' }}">
                                        </button>
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
                                                    style="background: rgba(0,0,0,0.35); left:0; right:0; padding:.5rem;">
                                                    <h6 class="mb-0">{{ $announcement->title }}</h6>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>

                                <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                <a href="{{ route('kejuruan.announcements') }}" class="btn btn-outline-primary btn-sm">Lihat
                                    Semua Pengumuman</a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <i class="bi bi-megaphone text-muted fs-2"></i>
                                <p class="text-muted mt-2 small">Belum ada pengumuman</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelatihan yang Diikuti -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom" style="background-color: orange;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-briefcase-fill me-2 text-success"></i>
                                Pelatihan yang Saya Ikuti
                            </h5>
                            <a href="{{ route('kejuruan.training-classes.index') }}" class="btn btn-outline-primary btn-sm">
                                Lihat Semua Pelatihan
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($trainingClasses && $trainingClasses->count() > 0)
                            <div class="row">
                                @foreach($trainingClasses->take(4) as $training)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border">
                                            <div class="card-body">
                                                <h6 class="card-title text-warning">
                                                    <i class="bi bi-briefcase me-2"></i>{{ $training->title }}
                                                </h6>
                                                <p class="card-text small text-muted mb-2">
                                                    {{ Str::limit($training->description, 80) }}
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>
                                                        {{ optional($training->trainer)->user->name ?? 'Belum ada pelatih' }}
                                                    </small>
                                                    <span
                                                        class="badge bg-{{ $training->pivot->status === 'enrolled' ? 'success' : 'secondary' }}">
                                                        {{ $training->pivot->status === 'enrolled' ? 'Aktif' : ucfirst($training->pivot->status) }}
                                                    </span>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar me-1"></i>
                                                        {{ $training->start_at ? \Carbon\Carbon::parse($training->start_at)->format('d M Y') : '-' }}
                                                        @if($training->end_at)
                                                            - {{ \Carbon\Carbon::parse($training->end_at)->format('d M Y') }}
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="mt-2 d-flex gap-2 align-items-center">
                                                    <a href="{{ route('kejuruan.training-classes.show', $training->id) }}"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye me-1"></i>Detail
                                                    </a>
                                                    <a href="{{ route('kejuruan.materials') }}?training_class_id={{ $training->id }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-folder me-1"></i>Materi
                                                    </a>
                                                    <small class="text-muted ms-auto">Kuota:
                                                        {{ $training->capacity ?? 'Unlimited' }} — Peserta:
                                                        {{ $training->students_count ?? 0 }}@if($training->capacity) — Sisa:
                                                        {{ max(0, ($training->capacity - ($training->students_count ?? 0))) }}@endif</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-briefcase text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">Anda belum mengikuti pelatihan apapun</p>
                                <a href="{{ route('kejuruan.training-classes.index') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>Lihat Pelatihan Tersedia
                                </a>
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
                    <div class="card-header border-bottom" style="background-color: orange;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-clipboard-data me-2 text-success"></i>
                                Nilai Terbaru
                            </h5>
                            <a href="{{ route('kejuruan.grades') }}" class="btn btn-outline-primary btn-sm">
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
                                            <th>Mata Pelajaran / Pelatihan</th>
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
                                                    <strong>{{ optional($grade->subject)->name ?? optional($grade->trainingClass)->name ?? '-' }}</strong>
                                                </td>
                                                <td>
                                                    @php
                                                        $rawType = data_get($grade, 'assessment_type') ?? data_get($grade, 'type') ?? '';
                                                        $jenisLabel = $rawType === 'midterm' ? 'UTS' : ($rawType === 'final' ? 'UAS' : ($rawType === 'daily' ? 'Harian' : ($rawType === 'project' ? 'Proyek' : ucfirst($rawType))));
                                                        $jenisBg = $rawType === 'midterm' ? 'warning' : ($rawType === 'final' ? 'danger' : 'info');
                                                        $jenisText = $jenisBg === 'warning' ? 'text-dark' : 'text-white';
                                                    @endphp
                                                    <span class="badge bg-{{ $jenisBg }} {{ $jenisText }}">
                                                        {{ $jenisLabel ?: '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $score = data_get($grade, 'score') ?? data_get($grade, 'value') ?? null;
                                                        $valBg = ($score !== null && $score >= 80) ? 'success' : (($score !== null && $score >= 70) ? 'warning' : 'danger');
                                                        $valText = $valBg === 'warning' ? 'text-dark' : 'text-white';
                                                    @endphp
                                                    <span class="badge bg-{{ $valBg }} {{ $valText }} fs-6">
                                                        {{ $score ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small
                                                        class="text-dark opacity-75">{{ $grade->assessment_date ? $grade->assessment_date->format('d/m/Y') : '-' }}</small>
                                                </td>
                                                <td>
                                                    <small
                                                        class="text-dark opacity-75">{{ data_get($grade, 'notes') ?? data_get($grade, 'description') ?? '-' }}</small>
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

        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        .student-page-wrapper {
            background: #fff5f5 !important;
            color: #111827 !important;
            min-height: 100vh;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .student-page-wrapper .icon.icon-shape.bg-primary {
            background: var(--primary) !important;
            color: #bababa !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            // Tooltip
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush