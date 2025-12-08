@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row min-vh-100">
            <div class="col-12">
                <!-- Academic Facilities Section -->
                <section class="py-5" style="background: rgba(255, 255, 255, 0.05);">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-bold text-white mb-3">Fasilitas Akademik</h2>
                            <p class="lead text-white opacity-75">Ruang belajar yang nyaman dan mendukung proses
                                pembelajaran optimal</p>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center h-100">
                                    <div class="mb-4">
                                        <img src="https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=300&h=200&fit=crop"
                                            alt="Classroom" class="img-fluid rounded mb-3"
                                            style="height: 150px; width: 100%; object-fit: cover;">
                                        <h5 class="fw-bold text-white mb-2">Ruang Kelas Modern</h5>
                                    </div>
                                    <p class="text-white opacity-90 small text-start">Ruang kelas yang dirancang khusus
                                        dengan pencahayaan optimal, AC, dan peralatan pembelajaran adaptif untuk berbagai
                                        jenis kebutuhan khusus.</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center h-100">
                                    <div class="mb-4">
                                        <img src="https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=300&h=200&fit=crop"
                                            alt="Library" class="img-fluid rounded mb-3"
                                            style="height: 150px; width: 100%; object-fit: cover;">
                                        <h5 class="fw-bold text-white mb-2">Perpustakaan Digital</h5>
                                    </div>
                                    <p class="text-white opacity-90 small text-start">Perpustakaan dengan koleksi buku
                                        braile, audiobook, dan materi pembelajaran digital yang dapat diakses dengan mudah
                                        oleh semua siswa.</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center h-100">
                                    <div class="mb-4">
                                        <img src="https://images.unsplash.com/photo-1562774053-701939374585?w=300&h=200&fit=crop"
                                            alt="Computer Lab" class="img-fluid rounded mb-3"
                                            style="height: 150px; width: 100%; object-fit: cover;">
                                        <h5 class="fw-bold text-white mb-2">Lab Komputer</h5>
                                    </div>
                                    <p class="text-white opacity-90 small text-start">Laboratorium komputer dengan perangkat
                                        lunak khusus dan teknologi assistive untuk mendukung pembelajaran digital dan
                                        keterampilan teknologi.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Therapy Facilities Section -->
                <section class="py-5">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-bold text-white mb-3">Fasilitas Terapi</h2>
                            <p class="lead text-white opacity-75">Ruang terapi khusus dengan peralatan modern untuk
                                mendukung perkembangan siswa</p>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-6 col-md-6">
                                <div class="stats-card">
                                    <div class="row g-3">
                                        <div class="col-7">
                                            <h5 class="fw-bold text-white mb-2">Ruang Terapi Wicara</h5>
                                            <p class="text-white opacity-90 small mb-3">Ruang kedap suara dengan peralatan
                                                terapi wicara modern dan media pembelajaran komunikasi.</p>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge"
                                                    style="background: rgba(245, 158, 11, 0.2); color: var(--orange); font-size: 0.7rem;">Kedap
                                                    Suara</span>
                                                <span class="badge"
                                                    style="background: rgba(245, 158, 11, 0.2); color: var(--orange); font-size: 0.7rem;">AC</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="stats-card">
                                    <div class="row g-3">
                                        <div class="col-7">
                                            <h5 class="fw-bold text-white mb-2">Ruang Fisioterapi</h5>
                                            <p class="text-white opacity-90 small mb-3">Ruang terapi fisik dengan peralatan
                                                exercise dan rehabilitasi untuk meningkatkan kemampuan motorik siswa.</p>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge"
                                                    style="background: rgba(245, 158, 11, 0.2); color: var(--orange); font-size: 0.7rem;">Matras
                                                    Terapi</span>
                                                <span class="badge"
                                                    style="background: rgba(245, 158, 11, 0.2); color: var(--orange); font-size: 0.7rem;">Alat
                                                    Exercise</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="stats-card">
                                    <div class="row g-3">
                                        <div class="col-7">
                                            <h5 class="fw-bold text-white mb-2">Ruang Terapi Okupasi</h5>
                                            <p class="text-white opacity-90 small mb-3">Ruang latihan keterampilan hidup
                                                sehari-hari dengan simulasi dapur, kamar mandi, dan area aktivitas.</p>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge"
                                                    style="background: rgba(245, 158, 11, 0.2); color: var(--orange); font-size: 0.7rem;">Simulasi
                                                    ADL</span>
                                                <span class="badge"
                                                    style="background: rgba(245, 158, 11, 0.2); color: var(--orange); font-size: 0.7rem;">Peralatan
                                                    Adaptif</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-6">
                                <div class="stats-card">
                                    <div class="row g-3">
                                        <div class="col-7">
                                            <h5 class="fw-bold text-white mb-2">Ruang Terapi Musik</h5>
                                            <p class="text-white opacity-90 small mb-3">Studio musik dengan berbagai alat
                                                musik dan sistem audio untuk terapi musik dan pengembangan kreativitas.</p>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge"
                                                    style="background: rgba(245, 158, 11, 0.2); color: var(--orange); font-size: 0.7rem;">Alat
                                                    Musik</span>
                                                <span class="badge"
                                                    style="background: rgba(245, 158, 11, 0.2); color: var(--orange); font-size: 0.7rem;">Sound
                                                    System</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Recreation & Support Facilities Section -->
                <section class="py-5" style="background: rgba(255, 255, 255, 0.05);">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-bold text-white mb-3">Fasilitas Penunjang</h2>
                            <p class="lead text-white opacity-75">Area rekreasi dan fasilitas penunjang untuk kehidupan
                                sekolah yang lengkap</p>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center">
                                    <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-tree-fill"></i>
                                    </div>
                                    <h5 class="fw-bold text-white mb-2">Taman Bermain</h5>
                                    <p class="text-white opacity-75 small mb-0">Area bermain outdoor yang aman dengan
                                        permainan yang disesuaikan untuk anak berkebutuhan khusus.</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center">
                                    <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-heart-pulse-fill"></i>
                                    </div>
                                    <h5 class="fw-bold text-white mb-2">Ruang UKS</h5>
                                    <p class="text-white opacity-75 small mb-0">Unit kesehatan sekolah dengan tenaga medis
                                        profesional dan peralatan medis dasar.</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center">
                                    <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-cup-hot-fill"></i>
                                    </div>
                                    <h5 class="fw-bold text-white mb-2">Kantin Sehat</h5>
                                    <p class="text-white opacity-75 small mb-0">Kantin dengan menu makanan sehat dan bergizi
                                        yang disesuaikan dengan kebutuhan dietary siswa.</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center">
                                    <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-car-front-fill"></i>
                                    </div>
                                    <h5 class="fw-bold text-white mb-2">Area Parkir</h5>
                                    <p class="text-white opacity-75 small mb-0">Tempat parkir yang luas dan aman dengan
                                        akses khusus untuk kendaraan penyandang disabilitas.</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center">
                                    <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-camera-video-fill"></i>
                                    </div>
                                    <h5 class="fw-bold text-white mb-2">CCTV & Keamanan</h5>
                                    <p class="text-white opacity-75 small mb-0">Sistem keamanan 24 jam dengan CCTV dan
                                        petugas security untuk menjamin keamanan siswa.</p>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card text-center">
                                    <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 60px; height: 60px;">
                                        <i class="bi bi-wifi"></i>
                                    </div>
                                    <h5 class="fw-bold text-white mb-2">WiFi & Internet</h5>
                                    <p class="text-white opacity-75 small mb-0">Akses internet cepat di seluruh area sekolah
                                        untuk mendukung pembelajaran digital.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection