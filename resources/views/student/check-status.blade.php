@extends('layouts.app')

@section('title', 'Cek Status Aplikasi')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0"><i class="fas fa-search me-2"></i>Cek Status Aplikasi</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('application.check-status') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="application_number" class="form-label">Nomor Aplikasi</label>
                                <input type="text" class="form-control @error('application_number') is-invalid @enderror"
                                    id="application_number" name="application_number" placeholder="Contoh: APP20240001"
                                    value="{{ old('application_number') }}" required>
                                @error('application_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Cek Status
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('home') }}" class="text-muted">
                                <i class="fas fa-arrow-left me-1"></i>Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection