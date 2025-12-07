@extends('layouts.app')

@section('content')
    <div class="container register-page">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-sm-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <div class="bg-white bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width: 70px; height: 70px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor"
                                        class="bi bi-person-plus-fill text-navy" viewBox="0 0 16 16">
                                        <path
                                            d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />
                                        <path fill-rule="evenodd"
                                            d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="fw-bold mb-2">{{ __('Buat Akun') }}</h3>
                            <p class="text-muted mb-0">{{ __('Daftar untuk memulai') }}</p>
                        </div>

                        <!-- Form -->
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <!-- Name -->
                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">{{ __('Full Name') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-person text-muted" viewBox="0 0 16 16">
                                            <path
                                                d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z" />
                                        </svg>
                                    </span>
                                    <input id="name" type="text"
                                        class="form-control border-start-0 @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" required autocomplete="name" autofocus
                                        placeholder="Enter your full name">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-envelope text-muted" viewBox="0 0 16 16">
                                            <path
                                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z" />
                                        </svg>
                                    </span>
                                    <input id="email" type="email"
                                        class="form-control border-start-0 @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email"
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
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-lock text-muted" viewBox="0 0 16 16">
                                            <path
                                                d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 8h6a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1z" />
                                        </svg>
                                    </span>
                                    <input id="password" type="password"
                                        class="form-control border-start-0 @error('password') is-invalid @enderror"
                                        name="password" required autocomplete="new-password"
                                        placeholder="Create a password">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <small class="text-muted">{{ __('Minimum 8 characters') }}</small>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label for="password-confirm"
                                    class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                            class="bi bi-shield-lock text-muted" viewBox="0 0 16 16">
                                            <path
                                                d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z" />
                                            <path
                                                d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415z" />
                                        </svg>
                                    </span>
                                    <input id="password-confirm" type="password" class="form-control border-start-0"
                                        name="password_confirmation" required autocomplete="new-password"
                                        placeholder="Confirm your password">
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-navy btn-lg rounded-3">
                                    {{ __('Create Account') }}
                                </button>
                            </div>

                            <!-- Login Link -->
                            <div class="text-center">
                                <span class="text-muted small">{{ __('Already have an account?') }}</span>
                                <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold">
                                    {{ __('Login') }}
                                </a>
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

        .bg-navy {
            background-color: #1a2947 !important;
        }

        .text-navy {
            color: #1a2947 !important;
        }

        .btn-navy {
            background-color: #e89e00;
            border-color: #47441a;
            color: white;
        }

        .btn-navy:hover {
            background-color: #0f1829;
            border-color: #0f1829;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(26, 41, 71, 0.4);
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        /* Global page-scoped orange icon rules for registration page */
        .register-page svg,
        .register-page .bi,
        .register-page i[class^="bi-"],
        .register-page i[class*=" bi-"] {
            color: var(--orange, #e89e00) !important;
            fill: currentColor !important;
            stroke: currentColor !important;
        }

        /* SVGs that include helper classes (like text-muted/text-navy) */
        .register-page svg.text-muted,
        .register-page svg.text-navy {
            color: var(--orange, #e89e00) !important;
            fill: currentColor !important;
            stroke: currentColor !important;
        }

        /* Make form icons orange to match accent color while keeping other icons global white */
        .input-group-text svg,
        .input-group-text .bi {
            color: var(--orange, #e89e00) !important;
            fill: currentColor;
            /* ensure SVG fill uses the color */
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(26, 41, 71, 0.15);
            border-color: #1a2947;
        }

        .btn-navy {
            padding: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        a {
            color: #1a2947;
        }

        a:hover {
            color: #0f1829;
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 2rem 1.5rem !important;
            }
        }
    </style>
@endsection