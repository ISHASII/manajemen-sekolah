@extends('layouts.app')

@section('content')
<div class="container login-page">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4 p-sm-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <div class="bg-warning bg-opacity-15 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-person-circle text-warning" viewBox="0 0 16 16">
                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                </svg>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-2">{{ __('LOGIN') }}</h3>
                        <p class="text-muted mb-0">{{ __('Masuk Menggunakan Akun yang Terdaftar') }}</p>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope text-muted" viewBox="0 0 16 16">
                                        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
                                    </svg>
                                </span>
                                <input id="email"
                                       type="email"
                                       class="form-control border-start-0 @error('email') is-invalid @enderror"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       autocomplete="email"
                                       autofocus
                                       placeholder="name@example.com">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">{{ __('Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock text-muted" viewBox="0 0 16 16">
                                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z"/>
                                    </svg>
                                </span>
                                <input id="password"
                                       type="password"
                                       class="form-control border-start-0 @error('password') is-invalid @enderror"
                                       name="password"
                                       required
                                       autocomplete="current-password"
                                       placeholder="Enter your password">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="remember"
                                       id="remember"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">
                                    {{ __('Ingat Saya') }}
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none small text-danger" href="{{ route('password.request') }}">
                                    {{ __('Lupa Password') }}
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3">
                                {{ __('Login') }}
                            </button>
                        </div>

                        <!-- Register Link -->
                        <div class="text-center">
                            <span class="text-muted small">{{ __("Tidak Memiliki Akun?") }}</span>
                            @if (Route::has('student.register'))
                                <a href="{{ route('student.register') }}" class="text-decoration-none small fw-semibold">
                                    {{ __('Daftar') }}
                                </a>
                            @else
                                <a href="{{ url('/register-student') }}" class="text-decoration-none small fw-semibold">{{ __('Sign Up') }}</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .min-vh-100 {
        min-height: 100vh;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
    }

    .input-group-text {
        background-color: #f8f9fa;
    }

    /* Make the login card white and text dark */
    .login-page .card {
        background: #ffffff !important;
        color: #000 !important;
    }

    .login-page .card h3,
    .login-page .card p,
    .login-page .card label,
    .login-page .card .form-check-label {
        color: #000 !important;
    }

    /* Replace any white text classes inside login card with dark colors */
    .login-page .text-white,
    .login-page .text-white-50 {
        color: #000 !important;
    }

    /* Page-scoped orange icon rules for login page */
    .login-page svg,
    .login-page .bi,
    .login-page i[class^="bi-"],
    .login-page i[class*=" bi-"] {
        color: var(--orange, #e89e00) !important;
        fill: currentColor !important;
        stroke: currentColor !important;
    }

    /* Target SVGs given helper classes so they don't retain other colors */
    .login-page svg.text-muted,
    .login-page svg.text-primary,
    .login-page svg.text-navy {
        color: var(--orange, #e89e00) !important;
        fill: currentColor !important;
        stroke: currentColor !important;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }

    .btn-primary {
        padding: 0.75rem;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 2rem 1.5rem !important;
        }
    }
</style>
@endsection
