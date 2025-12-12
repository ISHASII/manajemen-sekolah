@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <!-- Vision Mission Section -->
        <section class="py-5 bg-dark-subtle">
            <div class="container py-5">
                <div class="row g-4">
                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="vision-mission-card h-100">
                            <div class="icon-container mx-auto mb-4">
                                <i class="bi bi-eye-fill"></i>
                            </div>
                            <h3 class="fw-bold text-yellow-400 text-center mb-4" style="color: var(--orange);">Visi</h3>
                            @php
                                $vision = data_get($school, 'vision', 'Menjadi sekolah luar biasa terdepan yang memberikan pendidikan inklusif berkualitas tinggi, mengembangkan potensi setiap anak berkebutuhan khusus untuk menjadi individu yang mandiri, berkarakter, dan mampu berkontribusi positif bagi masyarakat.');
                            @endphp
                            <p class="text-white-50 text-center mb-0 px-lg-4">{{ $vision }}</p>
                        </div>
                    </div>

                    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="vision-mission-card h-100">
                            <div class="icon-container mx-auto mb-4">
                                <i class="bi bi-bullseye"></i>
                            </div>
                            <h3 class="fw-bold text-yellow-400 text-center mb-4">Misi</h3>
                            @php
                                $missionRaw = data_get($school, 'mission', 'Menyelenggarakan pendidikan yang adaptif dan individual\nMengembangkan keterampilan hidup dan kemandirian\nMembangun karakter dan nilai-nilai moral\nMenciptakan lingkungan belajar yang aman dan nyaman');
                                // split by newlines, trim empty entries
                                $missionItems = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $missionRaw))));
                            @endphp

                            @if(count($missionItems) > 0)
                                <ul class="mission-list list-unstyled mb-0">
                                    @foreach($missionItems as $index => $m)
                                        <li class="mb-3{{ $index === count($missionItems) - 1 ? ' mb-0' : '' }}">
                                            <span>{!! nl2br(e($m)) !!}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-white-50">{{ $missionRaw }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Values Section -->
        <section class="py-5">
            <div class="container py-5">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="display-5 fw-bold text-white mb-3">Nilai-Nilai Kami</h2>
                    <p class="lead text-white-50 mb-0">Fondasi yang menguatkan setiap langkah pendidikan kami</p>
                </div>

                <div class="row g-4">
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="value-card text-center h-100">
                            <div class="value-icon-wrapper mx-auto mb-4">
                                <i class="bi bi-heart-fill"></i>
                            </div>
                            <h5 class="fw-bold text-white mb-3">Kasih Sayang</h5>
                            <p class="text-white-50 mb-0">
                                Memberikan perhatian dan cinta kepada setiap anak dengan tulus dan penuh kesabaran
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="value-card text-center h-100">
                            <div class="value-icon-wrapper mx-auto mb-4">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h5 class="fw-bold text-white mb-3">Inklusif</h5>
                            <p class="text-white-50 mb-0">
                                Menerima semua anak tanpa diskriminasi dan menciptakan keberagaman yang harmonis
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="value-card text-center h-100">
                            <div class="value-icon-wrapper mx-auto mb-4">
                                <i class="bi bi-lightbulb-fill"></i>
                            </div>
                            <h5 class="fw-bold text-white mb-3">Inovatif</h5>
                            <p class="text-white-50 mb-0">
                                Menggunakan metode pembelajaran terdepan yang disesuaikan dengan perkembangan zaman
                            </p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="value-card text-center h-100">
                            <div class="value-icon-wrapper mx-auto mb-4">
                                <i class="bi bi-award-fill"></i>
                            </div>
                            <h5 class="fw-bold text-white mb-3">Profesional</h5>
                            <p class="text-white-50 mb-0">
                                Tenaga pendidik berkualitas dan berpengalaman yang terus mengembangkan kompetensi
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <style>
        :root {
            --orange: #ff6b35;
            /* use global primary (navbar) color if available */
            --blue: var(--primary, #0d6efd);
            --dark-bg: #1a1a2e;
            /* Soft navy card background that matches `--primary` in layout */
            --card-bg: linear-gradient(135deg, rgba(10, 29, 81, 0.08), rgba(30, 40, 81, 0.08));
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            min-height: 90vh;
            display: flex;
            align-items: center;
        }

        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(255, 107, 53, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 107, 53, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-badge {
            background: rgba(255, 107, 53, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 107, 53, 0.3);
            color: var(--orange);
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .hero-badge:hover {
            background: rgba(255, 107, 53, 0.25);
            transform: translateY(-2px);
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--orange) 0%, #ffa07a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Stats */
        .stat-item h3 {
            color: var(--orange);
            font-size: 2.5rem;
        }

        .stat-item small {
            font-size: 0.8rem;
            line-height: 1.4;
        }

        /* Hero Image */
        .hero-image-wrapper {
            padding: 20px;
        }

        .main-image-container {
            position: relative;
            z-index: 2;
            transition: transform 0.5s ease;
        }

        .main-image-container:hover {
            transform: translateY(-10px);
        }

        .main-image-container img {
            height: 500px;
            object-fit: cover;
        }

        /* Floating Card */
        .floating-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            bottom: -30px;
            right: 50px;
            max-width: 320px;
            z-index: 3;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .floating-card .icon-wrapper {
            width: 50px;
            height: 50px;
            background: rgba(255, 107, 53, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .floating-card .icon-wrapper i {
            color: var(--orange);
            font-size: 1.5rem;
        }

        /* Decorative Circles */
        .decoration-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.2), rgba(255, 107, 53, 0.05));
            z-index: 1;
        }

        .decoration-1 {
            width: 200px;
            height: 200px;
            top: -50px;
            right: -50px;
            animation: pulse 4s ease-in-out infinite;
        }

        .decoration-2 {
            width: 150px;
            height: 150px;
            bottom: -30px;
            left: -30px;
            animation: pulse 4s ease-in-out infinite 2s;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.3;
            }
        }

        /* Background Subtle */
        .bg-dark-subtle {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Vision Mission Cards */
        .vision-mission-card {
            background: linear-gradient(135deg, var(--blue) 0%, rgba(7, 17, 51, 0.95) 100%);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(7, 17, 51, 0.6);
            border-radius: 1.5rem;
            padding: 3rem 2rem;
            transition: all 0.4s ease;
            color: #fff;
        }

        .vision-mission-card:hover {
            background: linear-gradient(135deg, rgba(7, 17, 51, 0.98), rgba(10, 29, 81, 1));
            border-color: rgba(7, 17, 51, 0.9);
            transform: translateY(-10px);
        }

        .icon-container {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, rgba(10, 29, 81, 0.2), rgba(30, 40, 81, 0.1));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(10, 29, 81, 0.3);
        }

        .icon-container i {
            font-size: 2.5rem;
            color: var(--orange) !important;
        }

        .mission-list li {
            display: flex;
            align-items: flex-start;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
        }

        .mission-list li:hover {
            color: rgba(255, 255, 255, 0.9);
            transform: translateX(5px);
        }

        .mission-list i {
            color: var(--orange);
            font-size: 1.2rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Value Cards */
        .value-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            padding: 2.5rem 1.5rem;
            transition: all 0.4s ease;
        }

        .value-card:hover {
            background: linear-gradient(135deg, rgba(10, 29, 81, 0.12), rgba(30, 40, 81, 0.12));
            border-color: rgba(10, 29, 81, 0.35);
            transform: translateY(-10px);
        }

        .value-icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(10, 29, 81, 0.2), rgba(30, 40, 81, 0.1));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s ease;
        }

        .value-card:hover .value-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
            background: linear-gradient(135deg, rgba(10, 29, 81, 0.3), rgba(30, 40, 81, 0.15));
        }

        /* Make 'Visi' and 'Misi' headings orange for emphasis */
        .vision-mission-card h3 {
            color: var(--orange) !important;
        }

        .value-icon-wrapper i {
            font-size: 2rem;
            color: var(--orange);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--orange) 0%, #ff8c5a 100%);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.4);
        }

        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero-section {
                min-height: auto;
            }

            .main-image-container img {
                height: 350px;
            }

            .floating-card {
                position: relative;
                bottom: auto;
                right: auto;
                margin-top: 20px;
                max-width: 100%;
            }

            .decoration-circle {
                display: none;
            }

            .stat-item h3 {
                font-size: 2rem;
            }
        }

        @media (max-width: 575px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .stat-item h3 {
                font-size: 1.8rem;
            }

            .value-icon-wrapper {
                width: 60px;
                height: 60px;
            }

            .value-icon-wrapper i {
                font-size: 1.5rem;
            }
        }
    </style>

    <script>
        // Counter Animation
        document.addEventListener('DOMContentLoaded', function () {
            const counters = document.querySelectorAll('.counter');
            const speed = 200;

            const animateCounter = (counter) => {
                const target = +counter.getAttribute('data-target');
                const increment = target / speed;
                let count = 0;

                const updateCounter = () => {
                    count += increment;
                    if (count < target) {
                        counter.innerText = Math.ceil(count) + (target > 50 ? '+' : '%');
                        setTimeout(updateCounter, 10);
                    } else {
                        counter.innerText = target + (target > 50 ? '+' : '%');
                    }
                };

                updateCounter();
            };

            // Intersection Observer for counter animation
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target;
                        animateCounter(counter);
                        observer.unobserve(counter);
                    }
                });
            }, { threshold: 0.5 });

            counters.forEach(counter => observer.observe(counter));
        });
    </script>
@endsection
