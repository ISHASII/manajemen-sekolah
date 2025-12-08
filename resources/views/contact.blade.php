@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row min-vh-100">
            <div class="col-12">
                <!-- Hero Section -->
                <section class="hero-section py-5">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6 mb-5 mb-lg-0">
                                <div class="hero-badge mb-4">
                                    <i class="bi bi-star-fill me-2"></i>Hubungi Kami
                                </div>
                                <h1 class="display-4 fw-bold text-white mb-4">
                                    Kontak
                                    <span style="color: var(--orange);">SLB Harapan Bangsa</span>
                                </h1>
                                <p class="lead text-white opacity-90 mb-4">
                                    Kami siap membantu Anda dengan informasi lebih lanjut tentang program pendidikan
                                    inklusif yang kami tawarkan.
                                </p>

                                <!-- Contact Info -->
                                <div class="row g-4 mb-5">
                                    <div class="col-md-6">
                                        <div class="stats-card text-center">
                                            <i class="bi bi-telephone-fill"
                                                style="font-size: 2rem; color: var(--orange);"></i>
                                            <h6 class="mt-2 mb-1">Telepon</h6>
                                            <p class="small opacity-75">(021) 123-4567</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="stats-card text-center">
                                            <i class="bi bi-envelope-fill"
                                                style="font-size: 2rem; color: var(--orange);"></i>
                                            <h6 class="mt-2 mb-1">Email</h6>
                                            <p class="small opacity-75">info@slbharapanbangsa.sch.id</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="stats-card text-center">
                                            <i class="bi bi-geo-alt-fill"
                                                style="font-size: 2rem; color: var(--orange);"></i>
                                            <h6 class="mt-2 mb-1">Alamat</h6>
                                            <p class="small opacity-75">Jl. Pendidikan No. 123<br>Jakarta Selatan 12345</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="stats-card text-center">
                                            <i class="bi bi-clock-fill" style="font-size: 2rem; color: var(--orange);"></i>
                                            <h6 class="mt-2 mb-1">Jam Operasional</h6>
                                            <p class="small opacity-75">Senin - Jumat<br>07:00 - 15:00 WIB</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="position-relative">
                                    <div class="hero-image">
                                        <img src="https://images.unsplash.com/photo-1551836022-deb4988cc6c0?w=600&h=400&fit=crop"
                                            alt="Contact Us" class="img-fluid w-100"
                                            style="height: 400px; object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Map Section -->
                <section class="py-5">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-10">
                                <div class="text-center mb-5">
                                    <h2 class="display-6 fw-bold text-white mb-3">Lokasi Kami</h2>
                                    <p class="lead text-white opacity-75">Kunjungi sekolah kami untuk melihat fasilitas
                                        secara langsung</p>
                                </div>

                                <div class="stats-card">
                                    <div
                                        style="height: 400px; background: rgba(255, 255, 255, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                        <div class="text-center">
                                            <i class="bi bi-geo-alt-fill"
                                                style="font-size: 3rem; color: var(--orange);"></i>
                                            <h5 class="mt-3 text-white">Peta Lokasi</h5>
                                            <p class="text-white opacity-75">Jl. Pendidikan No. 123, Jakarta Selatan 12345
                                            </p>
                                            <a href="https://maps.google.com" target="_blank" class="btn btn-primary">
                                                <i class="bi bi-map me-2"></i>Buka di Google Maps
                                            </a>
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