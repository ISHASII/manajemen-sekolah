<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700,800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* Color system (blue navy primary) */
        :root {
            --primary: #0a1d51;
            /* navy blue */
            --primary-dark: #1e2851;
            /* darker navy */
            --orange: #f59e0b;
            /* yellow accent used for active underline */
            --yellow: #ffd700;
            /* orange accent */
            --dark-orange: #d97706;
            --muted-bg: #f8fafc;
            --text-muted: #64748b;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 80vh;
            display: flex;
            align-items: center;
            padding: 60px 0;
        }

        .feature-card {
            transition: transform 0.28s ease, box-shadow 0.28s ease;
            border-radius: 12px;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 40px rgba(45, 79, 61, 0.12);
        }

        .section-padding {
            padding: 72px 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.375rem;
            color: #ffffff !important;
        }

        /* Primary button with orange accent */
        .btn-primary {
            background: linear-gradient(135deg, var(--orange), var(--dark-orange)) !important;
            border: none !important;
            color: #000 !important;
            font-weight: 600;
            border-radius: 12px;
            padding: 12px 24px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--dark-orange), #b45309) !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }

        .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            font-weight: 600;
            padding: 12px 24px;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
            color: #ffffff;
            transform: translateY(-2px);
        }

        /* Small icon buttons used across the UI - keep consistent width/height and centered icon */
        .btn-icon {
            padding: 0.375rem 0.5rem !important;
            width: 42px !important;
            height: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            line-height: 1 !important;
        }

        .btn-icon i {
            font-size: 1rem !important;
        }

        /* subtle page background */
        html,
        body {
            height: 100%;
        }

        #app {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }

        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            color: #ffffff;
            @stack('scripts') -webkit-font-smoothing: antialiased;
            font-family: 'Inter', sans-serif;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.1) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            /* ensure it can be stacked above other content */
            z-index: 2000;
            /* keep navbar and its dropdowns above hero backgrounds */
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            display: inline-block;
            position: relative;
            padding-bottom: 0.65rem;
            /* space for underline */
        }

        .nav-link:hover {
            color: #ffffff !important;
        }

        /* Nav active underline - yellow */
        .nav-link.active::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 0;
            /* align at bottom of link */
            width: 60%;
            height: 3px;
            background: var(--yellow);
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(255, 215, 0, 0.18);
        }

        /* Ensure dropdown menus are above all other stacked content */
        .dropdown-menu {
            z-index: 3000 !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        /* Footer style */
        .site-footer {
            background: rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            color: #fff;
            margin-top: auto;
            /* ensure it stays at bottom when content is short */
        }

        .site-footer a {
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
        }

        /* Make cards blue and consistent across the site */
        .card {
            background: linear-gradient(135deg, rgba(10, 29, 81, 0.95) 0%, rgba(30, 40, 81, 0.95) 100%);
            color: #fff;
            border: none;
            box-shadow: 0 8px 24px rgba(2, 6, 23, 0.24);
        }

        .card .card-body {
            background: transparent;
            color: inherit;
        }

        .card .card-title,
        .card h3,
        .card h4,
        .card h5 {
            color: #fff;
        }

        .card .text-muted {
            color: rgba(255, 255, 255, 0.75) !important;
        }

        /* Keep card links readable */
        .card a {
            color: #fff;
            text-decoration: underline;
        }

        /* Accessibility toolbar tweaks */
        .accessibility-toolbar {
            position: fixed;
            top: 50%;
            right: -220px;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px 0 0 10px;
            padding: 14px;
            z-index: 9999;
            transition: right 0.28s ease;
            box-shadow: 0 8px 26px rgba(0, 0, 0, 0.1);
            width: 220px;
        }

        .accessibility-toolbar.open {
            right: 0;
        }

        .accessibility-toggle {
            position: absolute;
            left: -46px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 6px 0 0 6px;
            cursor: pointer;
        }

        .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.75%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* small responsive tweaks */
        @media (max-width: 768px) {
            .hero-section {
                padding: 40px 0;
            }

            .navbar-brand {
                font-size: 1.125rem;
            }
        }

        /* Accent colors for icons and headings across the site (using root variable --orange) */
        .vision-mission-card h3,
        .transition-card h5,
        .feature-card h5,
        .stats-card h4,
        .facility-card h6,
        .glass-card h6 {
            color: var(--orange) !important;
        }

        /* Force all Bootstrap icons to white by default */
        .bi,
        i[class^="bi-"],
        i[class*=" bi-"] {
            color: #ffffff !important;
            fill: #ffffff !important;
            /* for SVG icons */
        }

            {
                {
                -- Admin rules moved to layouts.admin --
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div id="app">
        <!-- Accessibility Toolbar -->
        <div id="accessibilityToolbar" class="accessibility-toolbar">
            <button class="accessibility-toggle" onclick="toggleAccessibility()">
                <i class="bi bi-universal-access-circle"></i>
            </button>
            <h6>Aksesibilitas</h6>
            <div class="d-grid gap-2">
                <button class="btn btn-sm btn-outline-primary" onclick="increaseFontSize()">
                    <i class="bi bi-zoom-in"></i> Perbesar Teks
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="decreaseFontSize()">
                    <i class="bi bi-zoom-out"></i> Perkecil Teks
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleHighContrast()">
                    <i class="bi bi-contrast"></i> Kontras Tinggi
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="toggleDyslexiaFont()">
                    <i class="bi bi-fonts"></i> Font Dyslexia
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="resetAccessibility()">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    SLB Harapan Bangsa
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') || request()->routeIs('welcome') ? 'active' : '' }}"
                                aria-current="{{ request()->routeIs('home') || request()->routeIs('welcome') ? 'page' : '' }}"
                                href="{{ route('home') }}">Beranda</a>
                        </li>
                        @unless(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isTeacher() || Auth::user()->isStudent()))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                                    aria-current="{{ request()->routeIs('about') ? 'page' : '' }}"
                                    href="{{ route('about') }}">Tentang</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('programs') ? 'active' : '' }}"
                                    aria-current="{{ request()->routeIs('programs') ? 'page' : '' }}"
                                    href="{{ route('programs') }}">Program</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('teachers') ? 'active' : '' }}"
                                    aria-current="{{ request()->routeIs('teachers') ? 'page' : '' }}"
                                    href="{{ route('teachers') }}">Tenaga Pendidik</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('facilities') ? 'active' : '' }}"
                                    aria-current="{{ request()->routeIs('facilities') ? 'page' : '' }}"
                                    href="{{ route('facilities') }}">Fasilitas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                                    aria-current="{{ request()->routeIs('contact') ? 'page' : '' }}"
                                    href="{{ route('contact') }}">Kontak</a>
                            </li>
                        @endunless
                    </ul>

                    <ul class="navbar-nav">
                        @guest
                            <li class="nav-item me-2">
                                <a class="btn btn-outline-light {{ request()->routeIs('application.status') ? 'active' : '' }}"
                                    href="{{ route('application.status') }}">
                                    <i class="bi bi-search me-1"></i>Cek Status
                                </a>
                            </li>
                            <li class="nav-item me-2">
                                <a class="btn btn-outline-light" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-primary" href="{{ route('student.register') }}">
                                    <i class="bi bi-person-plus-fill me-1"></i>Daftar Sekarang
                                </a>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="site-footer text-white py-5 mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <h5 class="mb-3">{{ isset($school) ? $school->name : 'Sekolah Kami' }}</h5>
                        <p class="text-light">
                            {{ isset($school) ? $school->description : 'Memberikan pendidikan terbaik untuk masa depan yang cerah.' }}
                        </p>
                        @if(isset($school) && $school->social_media)
                            <div class="d-flex gap-2">
                                @foreach($school->social_media as $platform => $url)
                                    <a href="{{ $url }}" class="btn btn-outline-light btn-sm">
                                        <i class="bi bi-{{ $platform }}"></i>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @unless(Auth::check() && (Auth::user()->isStudent() || Auth::user()->isTeacher() || Auth::user()->isAdmin()))
                        <div class="col-lg-2 mb-4">
                            <h6>Menu</h6>
                            <ul class="list-unstyled">
                                <li><a href="{{ route('about') }}" class="text-light text-decoration-none">Tentang</a></li>
                                <li><a href="{{ route('programs') }}" class="text-light text-decoration-none">Program</a>
                                </li>
                                <li><a href="{{ route('teachers') }}" class="text-light text-decoration-none">Guru</a></li>
                                <li><a href="{{ route('facilities') }}"
                                        class="text-light text-decoration-none">Fasilitas</a></li>
                            </ul>
                        </div>
                    @endunless
                    <div class="col-lg-3 mb-4">
                        <h6>Kontak</h6>
                        @if(isset($school))
                            <p class="text-light mb-1"><i class="bi bi-geo-alt me-2"></i>{{ $school->address }}</p>
                            <p class="text-light mb-1"><i class="bi bi-telephone me-2"></i>{{ $school->phone }}</p>
                            <p class="text-light"><i class="bi bi-envelope me-2"></i>{{ $school->email }}</p>
                        @endif
                    </div>
                    <div class="col-lg-3 mb-4">
                        <h6>Jam Operasional</h6>
                        <p class="text-light mb-1">Senin - Jumat: 07:00 - 16:00</p>
                        <p class="text-light mb-1">Sabtu: 07:00 - 12:00</p>
                        <p class="text-light">Minggu: Tutup</p>
                    </div>
                </div>
                <hr class="my-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-0">&copy; {{ date('Y') }} {{ isset($school) ? $school->name : 'Sekolah Kami' }}.
                            Semua hak dilindungi.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')

    <script>
        // Accessibility Functions
        let fontSize = 16;
        let isHighContrast = false;
        let isDyslexiaFont = false;

        function toggleAccessibility() {
            const toolbar = document.getElementById('accessibilityToolbar');
            toolbar.classList.toggle('open');
        }

        function increaseFontSize() {
            fontSize += 2;
            document.body.style.fontSize = fontSize + 'px';
        }

        function decreaseFontSize() {
            if (fontSize > 12) {
                fontSize -= 2;
                document.body.style.fontSize = fontSize + 'px';
            }
        }

        function toggleHighContrast() {
            isHighContrast = !isHighContrast;
            if (isHighContrast) {
                document.body.style.filter = 'contrast(200%) brightness(70%)';
            } else {
                document.body.style.filter = 'none';
            }
        }

        function toggleDyslexiaFont() {
            isDyslexiaFont = !isDyslexiaFont;
            if (isDyslexiaFont) {
                document.body.style.fontFamily = 'Arial, sans-serif';
            } else {
                document.body.style.fontFamily = '';
            }
        }

        function resetAccessibility() {
            fontSize = 16;
            isHighContrast = false;
            isDyslexiaFont = false;
            document.body.style.fontSize = '';
            document.body.style.filter = '';
            document.body.style.fontFamily = '';
        }

        // Close accessibility toolbar when clicking outside
        document.addEventListener('click', function (event) {
            const toolbar = document.getElementById('accessibilityToolbar');
            if (!toolbar.contains(event.target)) {
                toolbar.classList.remove('open');
            }
        });
    </script>

    <script>
        // Modal fallback initializer:
        // Some environments (ESM bundling / missing global) may prevent Bootstrap's
        // automatic data-api from opening modals. This small listener guarantees
        // modal buttons will open their target when clicked.
        (function () {
            function handleModalTriggerClick(e) {
                var btn = e.target.closest('[data-bs-toggle="modal"]');
                if (!btn) return;
                // Prevent double handling (native/data-api may already open it)
                e.preventDefault();

                var target = btn.getAttribute('data-bs-target') || btn.dataset.bsTarget;
                if (!target) return;

                try {
                    var modalEl = document.querySelector(target);
                    if (!modalEl) return;
                    // If Bootstrap is available, use its Modal API. Otherwise do nothing.
                    if (window.bootstrap && window.bootstrap.Modal) {
                        var m = new window.bootstrap.Modal(modalEl);
                        m.show();
                    } else {
                        // If bootstrap not present yet, attempt to dispatch a click after a tick
                        setTimeout(function () {
                            if (window.bootstrap && window.bootstrap.Modal) {
                                try { new window.bootstrap.Modal(modalEl).show(); } catch (err) { console.warn(err); }
                            }
                        }, 50);
                    }
                } catch (err) {
                    console.warn('modal fallback error', err);
                }
            }

            document.addEventListener('click', handleModalTriggerClick, true);
        })();
    </script>
</body>

</html>
