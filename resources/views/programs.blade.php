@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row min-vh-100">
            <div class="col-12">
                <!-- Programs Section -->
                <section class="py-5" style="background: rgba(255, 255, 255, 0.05);">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-bold text-white mb-3">Jenjang Pendidikan</h2>
                            <p class="lead text-white opacity-75">Program pendidikan yang komprehensif untuk setiap tahap
                                perkembangan</p>
                        </div>

                        <div class="row g-4">
                            <!-- SDLB Program -->
                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card h-100">
                                    <div class="text-center mb-4">
                                        <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="bi bi-book-fill" style="font-size: 2rem;"></i>
                                        </div>
                                        <h4 class="fw-bold text-white">SDLB</h4>
                                        <p class="text-white opacity-75">Sekolah Dasar Luar Biasa</p>
                                    </div>
                                    <ul class="list-unstyled text-white opacity-90">
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Usia 7-12 tahun</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Kurikulum adaptif</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Pembelajaran individual</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Terapi pendukung</li>
                                        <li class="mb-0"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Kegiatan ekstrakurikuler</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- SMPLB Program -->
                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card h-100">
                                    <div class="text-center mb-4">
                                        <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="bi bi-laptop-fill" style="font-size: 2rem;"></i>
                                        </div>
                                        <h4 class="fw-bold text-white">SMPLB</h4>
                                        <p class="text-white opacity-75">SMP Luar Biasa</p>
                                    </div>
                                    <ul class="list-unstyled text-white opacity-90">
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Usia 13-15 tahun</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Persiapan vokasi</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Pengembangan minat</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Keterampilan dasar</li>
                                        <li class="mb-0"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Bimbingan karir</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- SMALB Program -->
                            <div class="col-lg-4 col-md-6">
                                <div class="stats-card h-100">
                                    <div class="text-center mb-4">
                                        <div class="bg-warning bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                            style="width: 80px; height: 80px;">
                                            <i class="bi bi-tools" style="font-size: 2rem;"></i>
                                        </div>
                                        <h4 class="fw-bold text-white">SMALB</h4>
                                        <p class="text-white opacity-75">SMA Luar Biasa</p>
                                    </div>
                                    <ul class="list-unstyled text-white opacity-90">
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Usia 16-18 tahun</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Program vokasi</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Keterampilan kerja</li>
                                        <li class="mb-2"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Sertifikasi kompetensi</li>
                                        <li class="mb-0"><i class="bi bi-check-circle-fill me-2"
                                                style="color: var(--orange);"></i>Magang industri</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Specialized Programs Section -->
                <section class="py-5">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-bold text-white mb-3">Program Khusus</h2>
                            <p class="lead text-white opacity-75">Layanan terapi dan pengembangan khusus sesuai kebutuhan
                                individual</p>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-6 col-md-6">
                                <div class="stats-card">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px; flex-shrink: 0;">
                                            <i class="bi bi-chat-dots-fill"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-white mb-2">Terapi Wicara</h5>
                                            <p class="text-white opacity-90 mb-0">Program terapi untuk mengembangkan
                                                kemampuan komunikasi dan berbicara siswa dengan gangguan komunikasi.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="stats-card">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px; flex-shrink: 0;">
                                            <i class="bi bi-activity"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-white mb-2">Terapi Fisik</h5>
                                            <p class="text-white opacity-90 mb-0">Layanan fisioterapi untuk meningkatkan
                                                kemampuan motorik dan fungsi fisik siswa.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="stats-card">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px; flex-shrink: 0;">
                                            <i class="bi bi-puzzle-fill"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-white mb-2">Terapi Okupasi</h5>
                                            <p class="text-white opacity-90 mb-0">Program untuk mengembangkan keterampilan
                                                hidup sehari-hari dan kemandirian siswa.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6">
                                <div class="stats-card">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-warning bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px; flex-shrink: 0;">
                                            <i class="bi bi-music-note-beamed"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold text-white mb-2">Terapi Musik</h5>
                                            <p class="text-white opacity-90 mb-0">Menggunakan musik sebagai media terapi
                                                untuk mengembangkan kemampuan sosial dan emosional.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection