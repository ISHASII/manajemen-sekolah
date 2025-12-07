@extends('layouts.app')

@section('title', 'Pendaftaran Berhasil')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>

                        <h2 class="text-success mb-3">Pendaftaran Berhasil!</h2>

                        @if(session('application_number'))
                            <div class="alert alert-info">
                                <h5 class="mb-2">Nomor Aplikasi Anda:</h5>
                                <h3 class="text-primary"><strong>{{ session('application_number') }}</strong></h3>
                                <p class="mb-0"><small>Harap simpan nomor ini untuk pengecekan status aplikasi</small></p>
                            </div>
                        @endif

                        <div class="mb-4">
                            <p class="lead">Terima kasih telah mendaftar di sekolah kami. Aplikasi Anda sedang dalam proses
                                review.</p>
                            <p>Tim admin akan menghubungi Anda dalam 3-5 hari kerja untuk informasi lebih lanjut.</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('application.status') }}" class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="fas fa-search me-2"></i>Cek Status Aplikasi
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg w-100">
                                    <i class="fas fa-home me-2"></i>Kembali ke Beranda
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection