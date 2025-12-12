@extends('layouts.app')

@section('content')
    <div class="container reset-password-page">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Reset Password') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if(session('reset_link') && app()->isLocal())
                            <div class="alert alert-info">
                                <strong>Development reset link:</strong>
                                <div class="mt-2">
                                    <a href="{{ session('reset_link') }}">Open reset link</a>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Send Password Reset Link') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Reset Password page: card background white and dark text */
        .reset-password-page .card {
            background: #ffffff !important;
            color: #000 !important;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }

        .reset-password-page .card .card-header {
            background: #ffffff !important;
            color: #000 !important;
            font-weight: 600;
        }

        .reset-password-page .card label,
        .reset-password-page .card .col-form-label,
        .reset-password-page .card p,
        .reset-password-page .card h5,
        .reset-password-page .card .form-control,
        .reset-password-page .card .invalid-feedback {
            color: #000 !important;
        }

        .reset-password-page .form-control::placeholder {
            color: #6c757d !important;
        }
    </style>
@endpush
