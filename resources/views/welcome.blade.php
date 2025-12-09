@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="position-relative min-vh-100 overflow-hidden"
        style="background: linear-gradient(135deg, #1e3a5f 0%, #1c3453 50%, #2e5a7a 100%);">
        <!-- Decorative elements -->
        <div class="position-absolute w-100 h-100 overflow-hidden">
            <div class="position-absolute rounded-circle"
                style="top: 5rem; left: 2.5rem; width: 18rem; height: 18rem; background: rgba(245, 158, 11, 0.1); filter: blur(60px);">
            </div>
            <div class="position-absolute rounded-circle"
                style="bottom: 5rem; right: 2.5rem; width: 24rem; height: 24rem; background: rgba(245, 158, 11, 0.05); filter: blur(60px);">
            </div>
        </div>

        <div class="container position-relative py-5" style="z-index: 10;">
            <div class="row align-items-center" style="min-height: 70vh;">
                <!-- Left Content -->
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill mb-4"
                        style="background: rgba(245, 158, 11, 0.2);">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span class="small fw-medium text-warning">Sekolah Luar Biasa</span>
                    </div>

                    <h1 class="display-4 fw-bold text-white mb-4" style="line-height: 1.2;">
                        Selamat Datang di
                        <span class="d-block"
                            style="background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                            {{ $school->name ?? 'SLB Harapan Bangsa' }}
                        </span>
                    </h1>

                    <p class="lead mb-4" style="color: rgba(255, 255, 255, 0.8); max-width: 540px;">
                        {{ $school->description ?? 'Memberikan pendidikan terbaik untuk siswa berkebutuhan khusus dengan pendekatan yang inklusif dan berkualitas.' }}
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 mb-5">
                        <a href="{{ route('student.register') }}"
                            class="btn btn-light btn-lg px-4 py-3 rounded-3 fw-semibold d-inline-flex align-items-center gap-2 shadow-lg">
                            <i class="bi bi-people"></i>
                            Daftar Sekarang
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ route('about') }}"
                            class="btn btn-outline-light btn-lg px-4 py-3 rounded-3 fw-semibold d-inline-flex align-items-center gap-2">
                            <i class="bi bi-book"></i>
                            Pelajari Lebih Lanjut
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="d-flex flex-wrap gap-5 pt-3">

                    </div>
                </div>

                <!-- Right Image -->
                <div class="col-lg-6 position-relative">
                    <div class="position-relative rounded-4 overflow-hidden shadow-lg">
                        <img src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=600&h=400&fit=crop"
                            alt="Kegiatan Belajar di Sekolah" class="img-fluid w-100"
                            style="aspect-ratio: 16/9; object-fit: cover;">
                        <div class="position-absolute top-0 start-0 w-100 h-100"
                            style="background: linear-gradient(to top, rgba(30, 58, 95, 0.3), transparent);"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave divider -->
        <div class="position-absolute bottom-0 start-0 end-0">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z"
                    fill="#f8fafc" />
            </svg>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    @if($school && ($school->vision || $school->mission))
        <section class="py-5 py-lg-6" style="background: #f8fafc;">
            <div class="container py-5">
                <div class="row justify-content-center g-4">
                    @if($school->vision)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 p-4 p-lg-5 text-center transition-card">
                                <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-4"
                                    style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.1);">
                                    <i class="bi bi-eye fs-1"></i>
                                </div>
                                <h2 class="h3 fw-bold mb-3" style="color: var(--orange);">Visi</h2>
                                <p class="lead text-muted mb-0">{{ $school->vision }}</p>
                            </div>
                        </div>
                    @endif

                    @if($school->mission)
                        <div class="col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 p-4 p-lg-5 text-center transition-card">
                                <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-4"
                                    style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2);">
                                    <i class="bi bi-bullseye fs-1"></i>
                                </div>
                                <h2 class="h3 fw-bold mb-3" style="color: var(--orange);">Misi</h2>
                                <p class="lead text-muted mb-0">{{ $school->mission }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- Programs Section -->
    @if($school && $school->programs)
        <section class="py-5 py-lg-6" style="background: #f1f5f9;">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <span class="badge rounded-pill px-3 py-2 mb-3" style="background: rgba(30, 58, 95, 0.1); color: #1e3a5f;">
                        Program Unggulan
                    </span>
                    <h2 class="display-5 fw-bold mb-3" style="color: var(--orange);">Program Pendidikan</h2>
                    <p class="lead text-muted mx-auto" style="max-width: 600px;">
                        Program-program unggulan yang kami tawarkan untuk mengembangkan potensi setiap siswa
                    </p>
                </div>

                <div class="row g-4">
                    @foreach($school->programs as $program)
                        <div class="col-lg-4 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 p-4 transition-card">
                                <div class="mb-4 d-flex align-items-center justify-content-center rounded-3"
                                    style="width: 64px; height: 64px; background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);">
                                    <i class="bi bi-mortarboard-fill text-white fs-3"></i>
                                </div>
                                <h5 class="fw-bold mb-3" style="color: var(--orange);">{{ $program['name'] ?? $program }}</h5>
                                <p class="text-muted mb-0">{{ $program['description'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Facilities Section -->
    @if($school && $school->facilities)
        <section class="py-5 py-lg-6" style="background: #f8fafc;">
            <div class="container py-5">
                <div class="text-center mb-5">
                    <span class="badge rounded-pill px-3 py-2 mb-3"
                        style="background: rgba(245, 158, 11, 0.2); color: #d97706;">
                        Sarana Prasarana
                    </span>
                    <h2 class="display-5 fw-bold mb-3" style="color: #1e3a5f;">Fasilitas Sekolah</h2>
                    <p class="lead text-muted mx-auto" style="max-width: 600px;">
                        Fasilitas modern dan lengkap untuk mendukung proses pembelajaran
                    </p>
                </div>

                <div class="row g-3">
                    @foreach($school->facilities as $facility)
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 text-center facility-card"
                                style="cursor: pointer; transition: all 0.3s ease;">
                                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-3"
                                    style="width: 48px; height: 48px; background: rgba(30, 58, 95, 0.1);">
                                    <i class="bi bi-building fs-4"></i>
                                </div>
                                <h6 class="fw-semibold mb-0" style="color: var(--orange);">{{ $facility['name'] ?? $facility }}</h6>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Announcements Section -->
    @if(($announcements ?? collect())->count() > 0)
        <section class="py-5 py-lg-6" style="background: #f1f5f9;">
            <div class="container py-5">

                <!-- Title -->
                <div class="text-center mb-5">
                    <span class="badge rounded-pill px-3 py-2 mb-3" style="background: rgba(30, 58, 95, 0.1); color: #1e3a5f;">
                        Berita Terbaru
                    </span>
                    <h2 class="display-5 fw-bold mb-3" style="color: #1e3a5f;">Pengumuman Terbaru</h2>
                    <p class="lead text-muted mx-auto" style="max-width: 600px;">
                        Informasi penting dan terkini dari sekolah
                    </p>
                </div>

                <!-- Cards -->
                <div class="row g-4">

                    @foreach($announcements ?? collect() as $announcement)
                        <div class="col-lg-4 col-md-6">

                            <a href="{{ route('announcements.show', $announcement->id) }}" class="text-decoration-none text-reset">

                                <div class="card border-0 shadow-lg rounded-4 h-100 overflow-hidden transition-card">

                                    <!-- Image -->
                                    @if($announcement->image)
                                        <img src="{{ Storage::url($announcement->image) }}" class="card-img-top"
                                            alt="{{ $announcement->title }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center"
                                            style="height: 200px; background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);">
                                            <i class="bi bi-calendar-event text-white" style="font-size: 4rem; opacity: 0.5;"></i>
                                        </div>
                                    @endif

                                    <!-- Content -->
                                    <div class="card-body p-4">

                                        <div class="d-flex align-items-center gap-2 mb-3">

                                            @if($announcement->type === 'info')
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                                                    Informasi
                                                </span>

                                            @elseif($announcement->type === 'event')
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">
                                                    Acara
                                                </span>

                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1">
                                                    Prestasi
                                                </span>
                                            @endif

                                            <small class="text-muted">
                                                {{ $announcement->publish_date->format('d M Y') }}
                                            </small>
                                        </div>

                                        <h5 class="fw-bold mb-2" style="color: #1e3a5f;">
                                            {{ $announcement->title }}
                                        </h5>

                                        <p class="text-muted mb-0">
                                            {{ Str::limit($announcement->content, 150) }}
                                        </p>
                                    </div>

                                </div>
                            </a>

                        </div>
                    @endforeach

                </div>

            </div>
        </section>
    @endif


    <!-- CTA Section -->
    <section class="position-relative py-5 py-lg-6 overflow-hidden"
        style="background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);">
        <div class="position-absolute w-100 h-100">
            <div class="position-absolute rounded-circle"
                style="top: 2.5rem; right: 5rem; width: 16rem; height: 16rem; background: rgba(245, 158, 11, 0.1); filter: blur(60px);">
            </div>
            <div class="position-absolute rounded-circle"
                style="bottom: 2.5rem; left: 5rem; width: 20rem; height: 20rem; background: rgba(245, 158, 11, 0.05); filter: blur(60px);">
            </div>
        </div>

        <div class="container position-relative py-5" style="z-index: 10;">
            <div class="text-center mx-auto" style="max-width: 700px;">
                <h2 class="display-5 fw-bold text-white mb-4">Mulai Perjalanan Pendidikan</h2>
                <p class="lead mb-5" style="color: rgba(255, 255, 255, 0.8);">
                    Bergabunglah dengan ribuan siswa yang telah merasakan pendidikan berkualitas di sekolah kami
                </p>
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <a href="{{ route('student.register') }}"
                        class="btn btn-light btn-lg px-4 py-3 rounded-3 fw-semibold d-inline-flex align-items-center gap-2 shadow-lg">
                        <i class="bi bi-people"></i>
                        Daftar Sekarang
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="{{ route('contact') }}"
                        class="btn btn-outline-light btn-lg px-4 py-3 rounded-3 fw-semibold d-inline-flex align-items-center gap-2">
                        <i class="bi bi-telephone"></i>
                        Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Registration Process -->
    <section class="py-5 py-lg-6" style="background: #f8fafc;">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="badge rounded-pill px-3 py-2 mb-3"
                    style="background: rgba(245, 158, 11, 0.2); color: #d97706;">
                    Langkah Mudah
                </span>
                <h2 class="display-5 fw-bold mb-3" style="color: #1e3a5f;">Cara Mendaftar</h2>
                <p class="lead text-muted mx-auto" style="max-width: 600px;">
                    Proses pendaftaran yang mudah dan cepat
                </p>
            </div>

            <div class="row g-4 position-relative">
                <!-- Step 1 -->
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="position-relative">
                        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle shadow-lg"
                            style="width: 64px; height: 64px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);">
                            <span class="fs-4 fw-bold text-white">1</span>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: #1e3a5f;">Isi Formulir</h5>
                        <p class="text-muted">Lengkapi formulir pendaftaran online dengan data yang akurat</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="position-relative">
                        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle shadow-lg"
                            style="width: 64px; height: 64px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);">
                            <span class="fs-4 fw-bold text-white">2</span>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: #1e3a5f;">Upload Dokumen</h5>
                        <p class="text-muted">Siapkan dan upload dokumen yang diperlukan</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="position-relative">
                        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle shadow-lg"
                            style="width: 64px; height: 64px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);">
                            <span class="fs-4 fw-bold text-white">3</span>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: #1e3a5f;">Verifikasi</h5>
                        <p class="text-muted">Tim kami akan memverifikasi data dan dokumen Anda</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="col-lg-3 col-md-6 text-center">
                    <div class="position-relative">
                        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle shadow-lg"
                            style="width: 64px; height: 64px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);">
                            <span class="fs-4 fw-bold text-white">4</span>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: #1e3a5f;">Diterima</h5>
                        <p class="text-muted">Selamat! Anda resmi menjadi bagian dari keluarga besar kami</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .transition-card {
            transition: all 0.3s ease;
        }

        .transition-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px -10px rgba(30, 58, 95, 0.2) !important;
        }

        .facility-card:hover {
            background: #1e3a5f !important;
        }

        .facility-card:hover h6,
        .facility-card:hover i {
            color: #fff !important;
        }

        .facility-card:hover>div>div {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        /* Make program titles orange by default */
        .transition-card h5 {
            color: var(--orange) !important;
        }

        /* Keep facility headings orange, but icons will be white by default */
        .facility-card h6 {
            color: var(--orange) !important;
        }
    </style>
@endsection